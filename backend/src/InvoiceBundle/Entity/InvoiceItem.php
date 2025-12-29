<?php

namespace App\InvoiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'invoice_items')]
#[ORM\Index(name: 'idx_invoice_items_invoice', columns: ['invoice_id'])]
#[ORM\Index(name: 'idx_invoice_items_service', columns: ['service_id'])]
final class InvoiceItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Invoice $invoice;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Service $service = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: 'float')]
    private float $hours = 0.0;

    #[ORM\Column(name: 'unit_price', type: 'integer')]
    private int $unitPrice = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): void
    {
        $this->service = $service;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getHours(): float
    {
        return $this->hours;
    }

    public function setHours(float $hours): void
    {
        $this->hours = $hours;
    }

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }
}
