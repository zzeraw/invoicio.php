<?php

namespace App\ApiBundle\Tests\Security;

use App\ApiBundle\Repository\ApiTokenRepository;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class AuthTest extends KernelTestCase
{
    /**
     * Проверяет, что доступ к API эндпоинту запрещен без токена.
     */
    public function testApiEndpointRequiresToken(): void
    {
        $response = $this->request('/api/v1/secure', null);

        Assert::assertSame(401, $response->getStatusCode());
    }

    /**
     * Проверяет, что доступ к API эндпоинту разрешен с корректным токеном.
     */
    public function testApiEndpointAllowsValidToken(): void
    {
        $repository = $this->getApiTokenRepository();
        $repository->createToken('test-api-token', 'Test token', true);

        $response = $this->request('/api/v1/secure', 'test-api-token');

        Assert::assertSame(200, $response->getStatusCode());
    }

    /**
     * Проверяет, что некорректный токен возвращает 401.
     */
    public function testApiEndpointRejectsInvalidToken(): void
    {
        $response = $this->request('/api/v1/secure', 'invalid-token');

        Assert::assertSame(401, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        self::$kernel?->shutdown();
        self::$kernel = null;
        self::$booted = false;
    }

    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    private function getApiTokenRepository(): ApiTokenRepository
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        /** @var ApiTokenRepository $repository */
        $repository = $container->get(ApiTokenRepository::class);

        return $repository;
    }

    private function request(string $path, ?string $token): \Symfony\Component\HttpFoundation\Response
    {
        $kernel = self::bootKernel();

        $headers = [];
        if (null !== $token) {
            $headers['HTTP_X_API_TOKEN'] = $token;
        }

        return $kernel->handle(Request::create($path, 'GET', [], [], [], $headers));
    }
}
