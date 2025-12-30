<?php

namespace App\AppBundle\Tests\Controller;

use App\UserBundle\Enum\UserRoleEnum;
use App\UserBundle\Enum\UserStatusEnum;
use App\UserBundle\Repository\UserRepository;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class ClientControllerTest extends KernelTestCase
{
    /**
     * Проверяет полный CRUD через HTTP эндпоинты.
     */
    public function testClientCrudEndpoints(): void
    {
        $token = $this->createUserAndLogin('client.owner@app.test');

        $createResponse = $this->request(
            'POST',
            '/app/v1/clients',
            $token,
            [
                'name' => 'Client 1',
                'country_code' => 'RU',
                'legal_details' => ['key' => 'value'],
            ]
        );

        Assert::assertSame(201, $createResponse->getStatusCode());
        $created = $this->decodeJson($createResponse);
        Assert::assertSame('Client 1', $created['name'] ?? null);

        $listResponse = $this->request('GET', '/app/v1/clients', $token);
        Assert::assertSame(200, $listResponse->getStatusCode());
        $list = $this->decodeJson($listResponse);
        Assert::assertCount(1, $list);

        $clientId = (int) ($created['id'] ?? 0);
        Assert::assertTrue(0 < $clientId);

        $getResponse = $this->request('GET', '/app/v1/clients/' . $clientId, $token);
        Assert::assertSame(200, $getResponse->getStatusCode());

        $updateResponse = $this->request(
            'PUT',
            '/app/v1/clients/' . $clientId,
            $token,
            ['name' => 'Client 2']
        );
        Assert::assertSame(200, $updateResponse->getStatusCode());
        $updated = $this->decodeJson($updateResponse);
        Assert::assertSame('Client 2', $updated['name'] ?? null);

        $deleteResponse = $this->request('DELETE', '/app/v1/clients/' . $clientId, $token);
        Assert::assertSame(204, $deleteResponse->getStatusCode());

        $missingResponse = $this->request('GET', '/app/v1/clients/' . $clientId, $token);
        Assert::assertSame(404, $missingResponse->getStatusCode());
    }

    /**
     * Проверяет, что доступ без JWT запрещен.
     */
    public function testClientEndpointsRequireJwt(): void
    {
        $response = $this->request('GET', '/app/v1/clients', null);

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

    private function createUserAndLogin(string $email): string
    {
        $password = 'secret';

        $userRepository = $this->getUserRepository();
        $userRepository->createUser(
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            UserRoleEnum::USER->value,
            UserStatusEnum::ACTIVE->value
        );

        $loginResponse = $this->request('POST', '/app/v1/login', null, [
            'email' => $email,
            'password' => $password,
        ]);

        Assert::assertSame(200, $loginResponse->getStatusCode());

        $payload = $this->decodeJson($loginResponse);
        $token = (string) ($payload['token'] ?? '');
        if ('' === $token) {
            Assert::fail('JWT token is missing in login response.');
        }

        return $token;
    }

    private function request(string $method, string $path, ?string $token, ?array $payload = null): \Symfony\Component\HttpFoundation\Response
    {
        $kernel = self::bootKernel();

        $headers = [];
        if (null !== $token) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        }

        $content = null;
        if (null !== $payload) {
            $content = json_encode($payload);
            $headers['CONTENT_TYPE'] = 'application/json';
        }

        return $kernel->handle(Request::create($path, $method, [], [], [], $headers, (string) $content));
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJson(\Symfony\Component\HttpFoundation\Response $response): array
    {
        $data = json_decode((string) $response->getContent(), true);
        if (!is_array($data)) {
            Assert::fail('Response JSON is invalid.');
        }

        return $data;
    }

    private function getUserRepository(): UserRepository
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);

        return $userRepository;
    }
}
