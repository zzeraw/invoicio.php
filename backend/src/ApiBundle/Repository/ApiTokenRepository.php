<?php

namespace App\ApiBundle\Repository;

use App\ApiBundle\Dto\ApiTokenDto;
use App\ApiBundle\Entity\ApiToken;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

final readonly class ApiTokenRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function findActiveByToken(string $token): ?ApiTokenDto
    {
        $repository = $this->entityManager->getRepository(ApiToken::class);

        $apiToken = $repository->findOneBy([
            'token' => $token,
            'isActive' => true,
        ]);

        if (!$apiToken instanceof ApiToken) {
            return null;
        }

        return $this->convertEntityToApiTokenDto($apiToken);
    }

    public function createToken(string $token, ?string $label = null, bool $isActive = true): ApiTokenDto
    {
        $entity = new ApiToken();

        $entity->setToken($token);
        $entity->setLabel($label);
        $entity->setIsActive($isActive);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->convertEntityToApiTokenDto($entity);
    }

    private function convertEntityToApiTokenDto(ApiToken $entity): ApiTokenDto
    {
        $id = $entity->getId();
        if (null === $id) {
            throw new LogicException('ApiToken id is not set.');
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
