<?php

namespace App\ApiBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final class ApiTokenUser implements UserInterface
{
    public function __construct(
        private readonly string $identifier
    ) {
    }

    public function getRoles(): array
    {
        return ['ROLE_API'];
    }

    public function getUserIdentifier(): string
    {
        if ('' === $this->identifier) {
            throw new \LogicException('API token identifier is not set.');
        }

        return $this->identifier;
    }

    public function eraseCredentials(): void
    {
    }
}
