<?php

namespace App\InvoiceBundle\PublicService;

use App\InvoiceBundle\PublicInterface\ClientDtoInterface;
use App\InvoiceBundle\PublicInterface\CreateClientInputDtoInterface;
use App\InvoiceBundle\PublicInterface\UpdateClientInputDtoInterface;

interface ClientManageServiceInterface
{
    /**
     * @return array<int, ClientDtoInterface>
     */
    public function listForUser(int $userId): array;

    public function getForUser(int $userId, int $clientId): ?ClientDtoInterface;

    public function createForUser(int $userId, CreateClientInputDtoInterface $input): ClientDtoInterface;

    public function updateForUser(int $userId, int $clientId, UpdateClientInputDtoInterface $input): ?ClientDtoInterface;

    public function deleteForUser(int $userId, int $clientId): bool;
}
