<?php

namespace App\UserBundle\Service;

use App\UserBundle\Dto\CreateUserInputDto;
use App\UserBundle\Repository\UserRepository;
use InvalidArgumentException;

final class UserManageService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function createUser(CreateUserInputDto $input): int
    {
        $email = trim($input->getEmail());
        if ('' === $email) {
            throw new InvalidArgumentException('Email is required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email is invalid.');
        }
        if ('' === $input->getPassword()) {
            throw new InvalidArgumentException('Password is required.');
        }
        if ($this->userRepository->existsByEmail($email)) {
            throw new InvalidArgumentException('User with this email already exists.');
        }

        $passwordHash = password_hash($input->getPassword(), PASSWORD_DEFAULT);
        return $this->userRepository->createUser(
            $email,
            $passwordHash,
            $input->getRole()->value,
            $input->getStatus()->value
        );
    }
}
