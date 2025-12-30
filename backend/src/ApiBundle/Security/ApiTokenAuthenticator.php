<?php

namespace App\ApiBundle\Security;

use App\ApiBundle\Repository\ApiTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly ApiTokenRepository $apiTokenRepository)
    {
    }

    public function supports(Request $request): ?bool
    {
        $path = $request->getPathInfo();
        if (str_starts_with($path, '/api/v1/doc')) {
            return false;
        }

        return str_starts_with($path, '/api/v1');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $token = $this->extractToken($request);
        if (null === $token) {
            throw new CustomUserMessageAuthenticationException('API token is missing.');
        }

        $apiToken = $this->apiTokenRepository->findActiveByToken($token);
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('API token is invalid.');
        }

        return new SelfValidatingPassport(
            new UserBadge(
                $apiToken->getToken(),
                static fn () => new ApiTokenUser($apiToken->getToken())
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['message' => $exception->getMessageKey()],
            Response::HTTP_UNAUTHORIZED
        );
    }

    private function extractToken(Request $request): ?string
    {
        $authHeader = $request->headers->get('Authorization');
        if (is_string($authHeader) && str_starts_with($authHeader, 'Bearer ')) {
            return trim(substr($authHeader, 7));
        }

        $token = $request->headers->get('X-API-Token');
        if (is_string($token) && '' !== $token) {
            return $token;
        }

        return null;
    }
}
