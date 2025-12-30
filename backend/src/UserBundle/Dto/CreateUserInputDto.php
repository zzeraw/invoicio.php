<?php

namespace App\UserBundle\Dto;

final readonly class CreateUserInputDto
{
    public function __construct(
        private string $email,
        private string $password,
        private string $role,
        private string $status
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
