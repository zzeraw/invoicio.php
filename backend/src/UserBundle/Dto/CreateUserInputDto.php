<?php

namespace App\UserBundle\Dto;

use App\UserBundle\Enum\UserRoleEnum;
use App\UserBundle\Enum\UserStatusEnum;

final readonly class CreateUserInputDto
{
    public function __construct(
        private string $email,
        private string $password,
        private UserRoleEnum $role,
        private UserStatusEnum $status
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

    public function getRole(): UserRoleEnum
    {
        return $this->role;
    }

    public function getStatus(): UserStatusEnum
    {
        return $this->status;
    }
}
