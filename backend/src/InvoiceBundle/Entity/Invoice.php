<?php

namespace App\InvoiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'invoices')]
#[ORM\UniqueConstraint(name: 'uniq_invoices_number', columns: ['invoice_number'])]
#[ORM\Index(name: 'idx_invoices_user', columns: ['user_id'])]
#[ORM\Index(name: 'idx_invoices_client', columns: ['client_id'])]
#[ORM\HasLifecycleCallbacks]
final class Invoice
{
    public const VAT_WITH = 'with_vat';
    public const VAT_WITHOUT = 'without_vat';

    public const STATUS_ISSUED = 'issued';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELED = 'canceled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'user_id', type: 'integer')]
    private int $userId;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Client $client;

    #[ORM\Column(length: 5)]
    private string $language;

    #[ORM\Column(name: 'invoice_number', length: 64)]
    private string $invoiceNumber;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(name: 'issued_at', type: 'date_immutable')]
    private \DateTimeImmutable $issuedAt;

    #[ORM\Column(name: 'vat_mode', length: 20)]
    private string $vatMode = self::VAT_WITHOUT;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_ISSUED;

    /**
     * @var Collection<int, InvoiceItem>
     */
    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $items;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getIssuedAt(): \DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(\DateTimeImmutable $issuedAt): void
    {
        $this->issuedAt = $issuedAt;
    }

    public function getVatMode(): string
    {
        return $this->vatMode;
    }

    public function setVatMode(string $vatMode): void
    {
        $this->vatMode = $vatMode;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Collection<int, InvoiceItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
