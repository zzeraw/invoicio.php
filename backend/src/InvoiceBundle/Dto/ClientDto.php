<?php

namespace App\InvoiceBundle\Dto;

use App\InvoiceBundle\PublicInterface\ClientDtoInterface;

final readonly class ClientDto implements ClientDtoInterface
{
    /**
     * @param array<string, mixed>|null $legalDetails
     */
    public function __construct(
        private int $id,
        private int $userId,
        private string $name,
        private ?string $legalAddress,
        private ?string $countryCode,
        private ?string $taxId,
        private ?string $taxKpp,
        private ?string $registrationNumber,
        private ?array $legalDetails
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLegalAddress(): ?string
    {
        return $this->legalAddress;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function getTaxKpp(): ?string
    {
        return $this->taxKpp;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getLegalDetails(): ?array
    {
        return $this->legalDetails;
    }
}
