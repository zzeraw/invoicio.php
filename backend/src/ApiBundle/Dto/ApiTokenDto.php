<?php

namespace App\ApiBundle\Dto;

final readonly class ApiTokenDto
{
    public function __construct(
        private int $id,
        private string $token,
        private ?string $label,
        private bool $isActive,
        private \DateTimeImmutable $createdAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
