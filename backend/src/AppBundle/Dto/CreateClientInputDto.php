<?php

namespace App\AppBundle\Dto;

use App\InvoiceBundle\PublicInterface\ClientCreateDataInterface;

final readonly class CreateClientInputDto implements ClientCreateDataInterface
{
    /**
     * @param array<string, mixed>|null $legalDetails
     */
    public function __construct(
        private string $name,
        private ?string $legalAddress,
        private ?string $countryCode,
        private ?string $taxId,
        private ?string $taxKpp,
        private ?string $registrationNumber,
        private ?array $legalDetails
    ) {
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
