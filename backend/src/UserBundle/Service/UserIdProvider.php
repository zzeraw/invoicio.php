<?php

namespace App\UserBundle\Service;

use App\UserBundle\PublicService\UserIdProviderInterface;
use App\UserBundle\Repository\UserRepository;

final readonly class UserIdProvider implements UserIdProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function getIdByEmail(string $email): int
    {
        return $this->userRepository->getIdByEmail($email);
    }
}
