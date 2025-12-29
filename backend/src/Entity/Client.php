<?php

namespace App\Entity;

use App\Entity\Traits\SoftDeleteTrait;
use App\Entity\Traits\TimestampsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'clients')]
#[ORM\Index(name: 'idx_clients_user', columns: ['user_id'])]
#[ORM\HasLifecycleCallbacks]
final class Client
{
    use SoftDeleteTrait;
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(name: 'legal_address', type: 'text', nullable: true)]
    private ?string $legalAddress = null;

    #[ORM\Column(name: 'country_code', length: 2, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\Column(name: 'tax_id', length: 64, nullable: true)]
    private ?string $taxId = null;

    #[ORM\Column(name: 'tax_kpp', length: 64, nullable: true)]
    private ?string $taxKpp = null;

    #[ORM\Column(name: 'registration_number', length: 64, nullable: true)]
    private ?string $registrationNumber = null;

    #[ORM\Column(name: 'legal_details', type: 'json', nullable: true)]
    private ?array $legalDetails = null;

    /**
     * @var Collection<int, ClientAccount>
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: ClientAccount::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLegalAddress(): ?string
    {
        return $this->legalAddress;
    }

    public function setLegalAddress(?string $legalAddress): void
    {
        $this->legalAddress = $legalAddress;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): void
    {
        $this->taxId = $taxId;
    }

    public function getTaxKpp(): ?string
    {
        return $this->taxKpp;
    }

    public function setTaxKpp(?string $taxKpp): void
    {
        $this->taxKpp = $taxKpp;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(?string $registrationNumber): void
    {
        $this->registrationNumber = $registrationNumber;
    }

    public function getLegalDetails(): ?array
    {
        return $this->legalDetails;
    }

    public function setLegalDetails(?array $legalDetails): void
    {
        $this->legalDetails = $legalDetails;
    }

    /**
     * @return Collection<int, ClientAccount>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }
}
