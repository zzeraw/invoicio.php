<?php

namespace App\ApiBundle\Repository;

use App\ApiBundle\Dto\ApiTokenDto;
use App\ApiBundle\Entity\ApiToken;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ApiTokenRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function findActiveByToken(string $token): ?ApiTokenDto
    {
        $repository = $this->entityManager->getRepository(ApiToken::class);

        $apiToken = $repository->findOneBy(['token' => $token, 'isActive' => true]);

        if (!$apiToken instanceof ApiToken) {
            return null;
        }

        return $this->convertEntityToApiTokenDto($apiToken);
    }

    private function convertEntityToApiTokenDto(ApiToken $entity): ApiTokenDto
    {
        $id = $entity->getId();
        if ($id === null) {
            throw new \LogicException('ApiToken id is not set.');
        }

        return new ApiTokenDto(
            $id,
            $entity->getToken(),
            $entity->getLabel(),
            $entity->isActive(),
            $entity->getCreatedAt()
        );
    }
}
