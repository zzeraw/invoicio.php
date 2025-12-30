<?php

namespace App\InvoiceBundle\Tests\Service;

use App\InvoiceBundle\Dto\CreateClientInputDto;
use App\InvoiceBundle\Dto\UpdateClientInputDto;
use App\InvoiceBundle\Service\ClientManageService;
use App\UserBundle\Enum\UserRoleEnum;
use App\UserBundle\Enum\UserStatusEnum;
use App\UserBundle\Repository\UserRepository;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ClientManageServiceTest extends KernelTestCase
{
    /**
     * Проверяет, что CRUD работает в рамках текущего пользователя.
     */
    public function testCrudForUser(): void
    {
        $service = $this->getClientService();
        $userRepository = $this->getUserRepository();

        $userId = $userRepository->createUser(
            'client.owner@example.com',
            password_hash('secret', PASSWORD_DEFAULT),
            UserRoleEnum::USER->value,
            UserStatusEnum::ACTIVE->value
        );

        $created = $service->createForUser($userId, new CreateClientInputDto(
            'Client A',
            null,
            'RU',
            null,
            null,
            null,
            ['field' => 'value']
        ));

        Assert::assertSame($userId, $created->getUserId());
        Assert::assertSame('Client A', $created->getName());

        $list = $service->listForUser($userId);
        Assert::assertCount(1, $list);

        $found = $service->getForUser($userId, $created->getId());
        Assert::assertNotNull($found);
        Assert::assertSame($created->getId(), $found->getId());

        $updated = $service->updateForUser($userId, $created->getId(), new UpdateClientInputDto(
            'Client B',
            null,
            null,
            null,
            null,
            null,
            null,
            ['name' => true]
        ));

        Assert::assertNotNull($updated);
        Assert::assertSame('Client B', $updated->getName());

        $deleted = $service->deleteForUser($userId, $created->getId());
        Assert::assertTrue($deleted);
        Assert::assertNull($service->getForUser($userId, $created->getId()));
    }

    /**
     * Проверяет, что данные изолированы по user_id.
     */
    public function testUserIsolation(): void
    {
        $service = $this->getClientService();
        $userRepository = $this->getUserRepository();

        $userId1 = $userRepository->createUser(
            'user.one@example.com',
            password_hash('secret', PASSWORD_DEFAULT),
            UserRoleEnum::USER->value,
            UserStatusEnum::ACTIVE->value
        );
        $userId2 = $userRepository->createUser(
            'user.two@example.com',
            password_hash('secret', PASSWORD_DEFAULT),
            UserRoleEnum::USER->value,
            UserStatusEnum::ACTIVE->value
        );

        $client = $service->createForUser($userId1, new CreateClientInputDto(
            'Client X',
            null,
            null,
            null,
            null,
            null,
            null
        ));

        Assert::assertNull($service->getForUser($userId2, $client->getId()));
        Assert::assertCount(0, $service->listForUser($userId2));
    }

    /**
     * Проверяет, что пустое имя при обновлении приводит к ошибке.
     */
    public function testUpdateRejectsEmptyName(): void
    {
        $service = $this->getClientService();
        $userRepository = $this->getUserRepository();

        $userId = $userRepository->createUser(
            'user.empty@example.com',
            password_hash('secret', PASSWORD_DEFAULT),
            UserRoleEnum::USER->value,
            UserStatusEnum::ACTIVE->value
        );

        $client = $service->createForUser($userId, new CreateClientInputDto(
            'Client Y',
            null,
            null,
            null,
            null,
            null,
            null
        ));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Name is required.');

        $service->updateForUser($userId, $client->getId(), new UpdateClientInputDto(
            '',
            null,
            null,
            null,
            null,
            null,
            null,
            ['name' => true]
        ));
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

    private function getClientService(): ClientManageService
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        /** @var ClientManageService $service */
        $service = $container->get(ClientManageService::class);

        return $service;
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
