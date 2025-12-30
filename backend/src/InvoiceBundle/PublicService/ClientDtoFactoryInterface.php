<?php

namespace App\InvoiceBundle\PublicService;

use App\InvoiceBundle\PublicInterface\CreateClientInputDtoInterface;
use App\InvoiceBundle\PublicInterface\UpdateClientInputDtoInterface;

interface ClientDtoFactoryInterface
{
    /**
     * @param array<string, mixed>|null $legalDetails
     */
    public function createCreateInput(
        string $name,
        ?string $legalAddress,
        ?string $countryCode,
        ?string $taxId,
        ?string $taxKpp,
        ?string $registrationNumber,
        ?array $legalDetails
    ): CreateClientInputDtoInterface;

    /**
     * @param array<string, mixed>|null $legalDetails
     * @param array<string, bool> $fields
     */
    public function createUpdateInput(
        ?string $name,
        ?string $legalAddress,
        ?string $countryCode,
        ?string $taxId,
        ?string $taxKpp,
        ?string $registrationNumber,
        ?array $legalDetails,
        array $fields
    ): UpdateClientInputDtoInterface;
}
