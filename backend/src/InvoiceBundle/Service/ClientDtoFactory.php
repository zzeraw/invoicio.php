<?php

namespace App\InvoiceBundle\Service;

use App\InvoiceBundle\Dto\CreateClientInputDto;
use App\InvoiceBundle\Dto\UpdateClientInputDto;
use App\InvoiceBundle\PublicInterface\CreateClientInputDtoInterface;
use App\InvoiceBundle\PublicInterface\UpdateClientInputDtoInterface;
use App\InvoiceBundle\PublicService\ClientDtoFactoryInterface;

final class ClientDtoFactory implements ClientDtoFactoryInterface
{
    public function createCreateInput(
        string $name,
        ?string $legalAddress,
        ?string $countryCode,
        ?string $taxId,
        ?string $taxKpp,
        ?string $registrationNumber,
        ?array $legalDetails
    ): CreateClientInputDtoInterface {
        return new CreateClientInputDto(
            $name,
            $legalAddress,
            $countryCode,
            $taxId,
            $taxKpp,
            $registrationNumber,
            $legalDetails
        );
    }

    public function createUpdateInput(
        ?string $name,
        ?string $legalAddress,
        ?string $countryCode,
        ?string $taxId,
        ?string $taxKpp,
        ?string $registrationNumber,
        ?array $legalDetails,
        array $fields
    ): UpdateClientInputDtoInterface {
        return new UpdateClientInputDto(
            $name,
            $legalAddress,
            $countryCode,
            $taxId,
            $taxKpp,
            $registrationNumber,
            $legalDetails,
            $fields
        );
    }
}
