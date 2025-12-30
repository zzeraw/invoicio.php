<?php

namespace App\InvoiceBundle\Dto;

use App\InvoiceBundle\PublicInterface\UpdateClientInputDtoInterface;

final readonly class UpdateClientInputDto implements UpdateClientInputDtoInterface
{
    /**
     * @param array<string, mixed>|null $legalDetails
     * @param array<string, bool> $fields
     */
    public function __construct(
        private ?string $name,
        private ?string $legalAddress,
        private ?string $countryCode,
        private ?string $taxId,
        private ?string $taxKpp,
        private ?string $registrationNumber,
        private ?array $legalDetails,
        private array $fields
    ) {
    }

    public function getName(): ?string
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

    /**
     * @return array<string, bool>
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
