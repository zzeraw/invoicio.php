<?php

namespace App\UserBundle\Repository;

use App\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use RuntimeException;

final readonly class UserRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function existsByEmail(string $email): bool
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]) instanceof User;
    }

    public function getIdByEmail(string $email): int
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user instanceof User) {
            throw new LogicException('User not found.');
        }

        $id = $user->getId();
        if (null === $id) {
            throw new LogicException('User id is not set.');
        }

        return $id;
    }

    public function createUser(string $email, string $passwordHash, string $role, string $status): int
    {
        $user = new User();

        $user->setEmail($email);
        $user->setRole($role);
        $user->setStatus($status);
        $user->setPasswordHash($passwordHash);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $id = $user->getId();
        if (null === $id) {
            throw new RuntimeException('Failed to persist user.');
        }

        return $id;
    }
}
