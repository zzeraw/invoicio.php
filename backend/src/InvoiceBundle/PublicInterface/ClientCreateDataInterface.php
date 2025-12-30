<?php

namespace App\InvoiceBundle\PublicInterface;

interface ClientCreateDataInterface
{
    public function getName(): string;

    public function getLegalAddress(): ?string;

    public function getCountryCode(): ?string;

    public function getTaxId(): ?string;

    public function getTaxKpp(): ?string;

    public function getRegistrationNumber(): ?string;

    /**
     * @return array<string, mixed>|null
     */
    public function getLegalDetails(): ?array;
}
