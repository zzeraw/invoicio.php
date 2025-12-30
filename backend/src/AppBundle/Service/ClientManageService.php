<?php

namespace App\AppBundle\Service;

use App\AppBundle\Dto\CreateClientInputDto;
use App\AppBundle\Dto\UpdateClientInputDto;
use App\AppBundle\Security\CurrentUserResolver;
use App\InvoiceBundle\PublicInterface\ClientDtoInterface;
use App\InvoiceBundle\PublicService\ClientManageServiceInterface;
use InvalidArgumentException;

final class ClientManageService
{
    public function __construct(
        private readonly ClientManageServiceInterface $clientManageService,
        private readonly CurrentUserResolver $currentUserResolver
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listForCurrentUser(): array
    {
        $userId = $this->currentUserResolver->getUserId();
        $clients = $this->clientManageService->listForUser($userId);

        return array_map([$this, 'normalizeClient'], $clients);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getForCurrentUser(int $clientId): ?array
    {
        $userId = $this->currentUserResolver->getUserId();
        $client = $this->clientManageService->getForUser($userId, $clientId);

        if (null === $client) {
            return null;
        }

        return $this->normalizeClient($client);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createForCurrentUser(array $data): array
    {
        $name = $this->getOptionalString($data, 'name');
        if (null === $name || '' === $name) {
            throw new InvalidArgumentException('Name is required.');
        }

        $input = new CreateClientInputDto(
            $name,
            $this->getOptionalString($data, 'legal_address'),
            $this->getOptionalString($data, 'country_code'),
            $this->getOptionalString($data, 'tax_id'),
            $this->getOptionalString($data, 'tax_kpp'),
            $this->getOptionalString($data, 'registration_number'),
            $this->getOptionalArray($data, 'legal_details')
        );

        $userId = $this->currentUserResolver->getUserId();
        $client = $this->clientManageService->createForUser($userId, $input);

        return $this->normalizeClient($client);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    public function updateForCurrentUser(int $clientId, array $data): ?array
    {
        $input = new UpdateClientInputDto(
            $this->getOptionalString($data, 'name'),
            $this->getOptionalString($data, 'legal_address'),
            $this->getOptionalString($data, 'country_code'),
            $this->getOptionalString($data, 'tax_id'),
            $this->getOptionalString($data, 'tax_kpp'),
            $this->getOptionalString($data, 'registration_number'),
            $this->getOptionalArray($data, 'legal_details'),
            $this->getFieldFlags($data)
        );

        $userId = $this->currentUserResolver->getUserId();
        $client = $this->clientManageService->updateForUser($userId, $clientId, $input);

        if (null === $client) {
            return null;
        }

        return $this->normalizeClient($client);
    }

    public function deleteForCurrentUser(int $clientId): bool
    {
        $userId = $this->currentUserResolver->getUserId();

        return $this->clientManageService->deleteForUser($userId, $clientId);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getOptionalString(array $data, string $key): ?string
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        $value = $data[$key];
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be a string.', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    private function getOptionalArray(array $data, string $key): ?array
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        $value = $data[$key];
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be an array.', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, bool>
     */
    private function getFieldFlags(array $data): array
    {
        $fields = [];
        foreach (['name', 'legal_address', 'country_code', 'tax_id', 'tax_kpp', 'registration_number', 'legal_details'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[$this->toCamelCase($field)] = true;
            }
        }

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeClient(ClientDtoInterface $client): array
    {
        return [
            'id' => $client->getId(),
            'user_id' => $client->getUserId(),
            'name' => $client->getName(),
            'legal_address' => $client->getLegalAddress(),
            'country_code' => $client->getCountryCode(),
            'tax_id' => $client->getTaxId(),
            'tax_kpp' => $client->getTaxKpp(),
            'registration_number' => $client->getRegistrationNumber(),
            'legal_details' => $client->getLegalDetails(),
        ];
    }

    private function toCamelCase(string $value): string
    {
        $parts = explode('_', $value);
        $camel = array_shift($parts) ?: '';
        foreach ($parts as $part) {
            $camel .= ucfirst($part);
        }

        return $camel;
    }
}
