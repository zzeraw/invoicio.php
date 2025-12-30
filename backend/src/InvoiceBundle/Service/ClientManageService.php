<?php

namespace App\InvoiceBundle\Service;

use App\InvoiceBundle\Dto\ClientDto;
use App\InvoiceBundle\Dto\CreateClientInputDto;
use App\InvoiceBundle\Dto\UpdateClientInputDto;
use App\InvoiceBundle\Repository\ClientRepository;
use InvalidArgumentException;

final class ClientManageService
{
    public function __construct(
        private readonly ClientRepository $clientRepository
    ) {
    }

    /**
     * @return array<int, ClientDto>
     */
    public function listForUser(int $userId): array
    {
        return $this->clientRepository->listForUser($userId);
    }

    public function getForUser(int $userId, int $clientId): ?ClientDto
    {
        return $this->clientRepository->getForUser($userId, $clientId);
    }

    public function createForUser(int $userId, CreateClientInputDto $input): ClientDto
    {
        if ('' === $input->getName()) {
            throw new InvalidArgumentException('Name is required.');
        }

        return $this->clientRepository->createForUser($userId, $input);
    }

    public function updateForUser(int $userId, int $clientId, UpdateClientInputDto $input): ?ClientDto
    {
        if ($input->hasField('name') && (null === $input->getName() || '' === $input->getName())) {
            throw new InvalidArgumentException('Name is required.');
        }

        return $this->clientRepository->updateForUser($userId, $clientId, $input);
    }

    public function deleteForUser(int $userId, int $clientId): bool
    {
        return $this->clientRepository->deleteForUser($userId, $clientId);
    }
}
