<?php

namespace App\UserBundle\Service;

use App\UserBundle\Dto\CreateUserInputDto;
use App\UserBundle\Repository\UserRepository;
use InvalidArgumentException;
use RuntimeException;

final class UserManageService
{
    public const DEFAULT_ROLE = 'admin';
    public const DEFAULT_STATUS = 'active';

    private const ALLOWED_ROLES = ['admin', 'user'];
    private const ALLOWED_STATUSES = ['active', 'blocked'];

    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function createUser(CreateUserInputDto $input): int
    {
        $email = trim($input->getEmail());
        if ($email === '') {
            throw new InvalidArgumentException('Email is required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email is invalid.');
        }
        if (!in_array($input->getRole(), self::ALLOWED_ROLES, true)) {
            throw new InvalidArgumentException('Role is invalid.');
        }
        if (!in_array($input->getStatus(), self::ALLOWED_STATUSES, true)) {
            throw new InvalidArgumentException('Status is invalid.');
        }
        if ('' === $input->getPassword()) {
            throw new InvalidArgumentException('Password is required.');
        }
        if ($this->userRepository->existsByEmail($email)) {
            throw new InvalidArgumentException('User with this email already exists.');
        }

        $passwordHash = password_hash($input->getPassword(), PASSWORD_DEFAULT);
        if (false === $passwordHash) {
            throw new RuntimeException('Failed to hash password.');
        }

        return $this->userRepository->createUser($email, $passwordHash, $input->getRole(), $input->getStatus());
    }
}
