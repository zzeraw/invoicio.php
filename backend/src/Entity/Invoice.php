<?php

namespace App\Entity;

use App\Entity\Traits\SoftDeleteTrait;
use App\Entity\Traits\TimestampsTrait;
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
    use SoftDeleteTrait;
    use TimestampsTrait;

    public const VAT_WITH = 'with_vat';
    public const VAT_WITHOUT = 'without_vat';

    public const STATUS_ISSUED = 'issued';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELED = 'canceled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

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

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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
}
