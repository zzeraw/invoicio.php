<?php

namespace App\ApiBundle\Repository;

use App\ApiBundle\Entity\ApiToken;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ApiTokenRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function findActiveByToken(string $token): ?ApiToken
    {
        $repository = $this->entityManager->getRepository(ApiToken::class);

        $apiToken = $repository->findOneBy(['token' => $token, 'isActive' => true]);
        if (!$apiToken instanceof ApiToken) {
            return null;
        }

        return $apiToken;
    }
}
