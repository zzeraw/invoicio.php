<?php

namespace App\AppBundle\Security;

use App\UserBundle\PublicService\UserIdProviderInterface;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class CurrentUserResolver
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserIdProviderInterface $userIdProvider
    ) {
    }

    public function getUserId(): int
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            throw new LogicException('Security token is missing.');
        }

        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            $identifier = $user->getUserIdentifier();
        } elseif (is_string($user)) {
            $identifier = $user;
        } else {
            throw new LogicException('Authenticated user is missing.');
        }

        if ('' === $identifier) {
            throw new LogicException('User identifier is missing.');
        }

        return $this->userIdProvider->getIdByEmail($identifier);
    }
}
