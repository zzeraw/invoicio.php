<?php

namespace App\AppBundle\Tests\Security;

use App\UserBundle\Enum\UserRoleEnum;
use App\UserBundle\Enum\UserStatusEnum;
use App\UserBundle\Repository\UserRepository;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class AuthTest extends KernelTestCase
{
    /**
     * Проверяет, что активный пользователь получает JWT и может вызвать защищенный эндпоинт.
     */
    public function testActiveUserCanAccessSecureEndpoint(): void
    {
        $userRepository = $this->getUserRepository();
        $email = 'app.user@example.com';
        $password = 'secret';

        $userRepository->createUser(
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            UserRoleEnum::USER->value,
            UserStatusEnum::ACTIVE->value
        );

        $token = $this->login('/app/v1/login', $email, $password);
        $response = $this->request('/app/v1/secure', $token);

        Assert::assertSame(200, $response->getStatusCode());
    }

    /**
     * Проверяет, что без JWT доступ к защищенному эндпоинту запрещен.
     */
    public function testSecureEndpointRequiresJwt(): void
    {
        $response = $this->request('/app/v1/secure', null);

        Assert::assertSame(401, $response->getStatusCode());
    }

    /**
     * Проверяет, что заблокированный пользователь не может получить JWT.
     */
    public function testBlockedUserCannotLogin(): void
    {
        $userRepository = $this->getUserRepository();
        $email = 'app.blocked@example.com';
        $password = 'secret';

        $userRepository->createUser(
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            UserRoleEnum::USER->value,
            UserStatusEnum::BLOCKED->value
        );

        $response = $this->requestLogin('/app/v1/login', $email, $password);

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

    private function getUserRepository(): UserRepository
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);

        return $userRepository;
    }

    private function login(string $path, string $email, string $password): string
    {
        $response = $this->requestLogin($path, $email, $password);
        Assert::assertSame(200, $response->getStatusCode());

        $payload = json_decode((string) $response->getContent(), true);
        if (!is_array($payload) || !isset($payload['token'])) {
            Assert::fail('JWT token is missing in response.');
        }

        $token = (string) $payload['token'];
        if ('' === $token) {
            Assert::fail('JWT token is empty in response.');
        }

        return $token;
    }

    private function request(string $path, ?string $token): \Symfony\Component\HttpFoundation\Response
    {
        $kernel = self::bootKernel();

        $headers = [];
        if (null !== $token) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        }

        return $kernel->handle(Request::create($path, 'GET', [], [], [], $headers));
    }

    private function requestLogin(string $path, string $email, string $password): \Symfony\Component\HttpFoundation\Response
    {
        $kernel = self::bootKernel();

        $payload = json_encode(['email' => $email, 'password' => $password]);
        $headers = ['CONTENT_TYPE' => 'application/json'];

        return $kernel->handle(Request::create($path, 'POST', [], [], [], $headers, (string) $payload));
    }
}
