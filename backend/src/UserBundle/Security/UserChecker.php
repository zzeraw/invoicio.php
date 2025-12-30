<?php

namespace App\UserBundle\Security;

use App\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (User::STATUS_BLOCKED === $user->getStatus()) {
            throw new CustomUserMessageAccountStatusException('User is blocked.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
