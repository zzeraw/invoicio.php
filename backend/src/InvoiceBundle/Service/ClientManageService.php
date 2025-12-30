<?php

namespace App\InvoiceBundle\Service;

use App\InvoiceBundle\PublicInterface\ClientCreateDataInterface;
use App\InvoiceBundle\PublicInterface\ClientDtoInterface;
use App\InvoiceBundle\PublicInterface\ClientUpdateDataInterface;
use App\InvoiceBundle\PublicService\ClientManageServiceInterface;
use App\InvoiceBundle\Repository\ClientRepository;
use InvalidArgumentException;

final class ClientManageService implements ClientManageServiceInterface
{
    public function __construct(
        private readonly ClientRepository $clientRepository
    ) {
    }

    /**
     * @return array<int, ClientDtoInterface>
     */
    public function listForUser(int $userId): array
    {
        return $this->clientRepository->listForUser($userId);
    }

    public function getForUser(int $userId, int $clientId): ?ClientDtoInterface
    {
        return $this->clientRepository->getForUser($userId, $clientId);
    }

    public function createForUser(int $userId, ClientCreateDataInterface $input): ClientDtoInterface
    {
        if ('' === $input->getName()) {
            throw new InvalidArgumentException('Name is required.');
        }

        return $this->clientRepository->createForUser($userId, $input);
    }

    public function updateForUser(int $userId, int $clientId, ClientUpdateDataInterface $input): ?ClientDtoInterface
    {
        if ($this->hasField($input->getFields(), 'name') && (null === $input->getName() || '' === $input->getName())) {
            throw new InvalidArgumentException('Name is required.');
        }

        return $this->clientRepository->updateForUser($userId, $clientId, $input);
    }

    public function deleteForUser(int $userId, int $clientId): bool
    {
        return $this->clientRepository->deleteForUser($userId, $clientId);
    }

    /**
     * @param array<string, bool> $fields
     */
    private function hasField(array $fields, string $name): bool
    {
        return true === ($fields[$name] ?? false);
    }
}
