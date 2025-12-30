<?php

namespace App\InvoiceBundle\Repository;

use App\InvoiceBundle\Dto\ClientDto;
use App\InvoiceBundle\Dto\CreateClientInputDto;
use App\InvoiceBundle\Dto\UpdateClientInputDto;
use App\InvoiceBundle\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use LogicException;

final readonly class ClientRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @return array<int, ClientDto>
     */
    public function listForUser(int $userId): array
    {
        $repository = $this->entityManager->getRepository(Client::class);
        $clients = $repository->findBy(
            ['userId' => $userId, 'deletedAt' => null],
            ['id' => 'ASC']
        );

        $result = [];
        foreach ($clients as $client) {
            if (!$client instanceof Client) {
                continue;
            }
            $result[] = $this->convertEntityToDto($client);
        }

        return $result;
    }

    public function getForUser(int $userId, int $clientId): ?ClientDto
    {
        $repository = $this->entityManager->getRepository(Client::class);
        $client = $repository->findOneBy(
            ['id' => $clientId, 'userId' => $userId, 'deletedAt' => null]
        );

        if (!$client instanceof Client) {
            return null;
        }

        return $this->convertEntityToDto($client);
    }

    public function createForUser(int $userId, CreateClientInputDto $input): ClientDto
    {
        $client = new Client();
        $client->setUserId($userId);
        $client->setName($input->getName());
        $client->setLegalAddress($input->getLegalAddress());
        $client->setCountryCode($input->getCountryCode());
        $client->setTaxId($input->getTaxId());
        $client->setTaxKpp($input->getTaxKpp());
        $client->setRegistrationNumber($input->getRegistrationNumber());
        $client->setLegalDetails($input->getLegalDetails());

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $this->convertEntityToDto($client);
    }

    public function updateForUser(int $userId, int $clientId, UpdateClientInputDto $input): ?ClientDto
    {
        $repository = $this->entityManager->getRepository(Client::class);
        $client = $repository->findOneBy(
            ['id' => $clientId, 'userId' => $userId, 'deletedAt' => null]
        );

        if (!$client instanceof Client) {
            return null;
        }

        if ($input->hasField('name')) {
            $name = $input->getName();
            if (null === $name || '' === $name) {
                throw new InvalidArgumentException('Name is required.');
            }
            $client->setName($name);
        }

        if ($input->hasField('legalAddress')) {
            $client->setLegalAddress($input->getLegalAddress());
        }

        if ($input->hasField('countryCode')) {
            $client->setCountryCode($input->getCountryCode());
        }

        if ($input->hasField('taxId')) {
            $client->setTaxId($input->getTaxId());
        }

        if ($input->hasField('taxKpp')) {
            $client->setTaxKpp($input->getTaxKpp());
        }

        if ($input->hasField('registrationNumber')) {
            $client->setRegistrationNumber($input->getRegistrationNumber());
        }

        if ($input->hasField('legalDetails')) {
            $client->setLegalDetails($input->getLegalDetails());
        }

        $this->entityManager->flush();

        return $this->convertEntityToDto($client);
    }

    public function deleteForUser(int $userId, int $clientId): bool
    {
        $repository = $this->entityManager->getRepository(Client::class);
        $client = $repository->findOneBy(
            ['id' => $clientId, 'userId' => $userId, 'deletedAt' => null]
        );

        if (!$client instanceof Client) {
            return false;
        }

        $client->setDeletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return true;
    }

    private function convertEntityToDto(Client $entity): ClientDto
    {
        $id = $entity->getId();
        if (null === $id) {
            throw new LogicException('Client id is not set.');
        }

        return new ClientDto(
            $id,
            $entity->getUserId(),
            $entity->getName(),
            $entity->getLegalAddress(),
            $entity->getCountryCode(),
            $entity->getTaxId(),
            $entity->getTaxKpp(),
            $entity->getRegistrationNumber(),
            $entity->getLegalDetails()
        );
    }
}
