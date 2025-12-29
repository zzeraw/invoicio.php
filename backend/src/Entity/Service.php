<?php

namespace App\Entity;

use App\Entity\Traits\SoftDeleteTrait;
use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'services')]
#[ORM\Index(name: 'idx_services_user', columns: ['user_id'])]
#[ORM\HasLifecycleCallbacks]
final class Service
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

    #[ORM\Column(name: 'name_ru', length: 255, nullable: true)]
    private ?string $nameRu = null;

    #[ORM\Column(name: 'name_en', length: 255, nullable: true)]
    private ?string $nameEn = null;

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

    public function getNameRu(): ?string
    {
        return $this->nameRu;
    }

    public function setNameRu(?string $nameRu): void
    {
        $this->nameRu = $nameRu;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(?string $nameEn): void
    {
        $this->nameEn = $nameEn;
    }
}
