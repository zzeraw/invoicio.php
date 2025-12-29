<?php

namespace App\UserBundle\Entity;

use App\Shared\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'uniq_users_email', columns: ['email'])]
#[ORM\HasLifecycleCallbacks]
final class User
{
    use TimestampsTrait;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_ACTIVE;

    #[ORM\Column(length: 50)]
    private string $role = self::ROLE_USER;

    #[ORM\Column(name: 'password_hash', length: 255)]
    private string $passwordHash;

    #[ORM\Column(name: 'password_reset_token', length: 255, nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(name: 'password_reset_expires_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $passwordResetExpiresAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    public function getPasswordResetExpiresAt(): ?\DateTimeImmutable
    {
        return $this->passwordResetExpiresAt;
    }

    public function setPasswordResetExpiresAt(?\DateTimeImmutable $passwordResetExpiresAt): void
    {
        $this->passwordResetExpiresAt = $passwordResetExpiresAt;
    }
}
