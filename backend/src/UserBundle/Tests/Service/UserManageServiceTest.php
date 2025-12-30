<?php

namespace App\UserBundle\Tests\Service;

use App\UserBundle\Dto\CreateUserInputDto;
use App\UserBundle\Repository\UserRepository;
use App\UserBundle\Service\UserManageService;
use App\UserBundle\Tests\Fixtures\AliceFixtureLoader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserManageServiceTest extends KernelTestCase
{
    private UserManageService $service;
    private UserRepository $userRepository;
    private AliceFixtureLoader $fixtures;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        /** @var UserManageService $service */
        $service = $container->get(UserManageService::class);
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);

        $this->service = $service;
        $this->userRepository = $userRepository;
        $this->fixtures = new AliceFixtureLoader($entityManager);
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

    /**
     * Проверяет, что новый пользователь успешно создается и сохраняется в базе.
     */
    public function testCreateUserCreatesRecord(): void
    {
        $input = new CreateUserInputDto('user@example.com', 'secret', 'admin', 'active');
        $userId = $this->service->createUser($input);

        $this->assertGreaterThan(0, $userId);
        $this->assertTrue($this->userRepository->existsByEmail('user@example.com'));
    }

    /**
     * Проверяет, что при создании пользователя с дубликатом email возвращается ошибка.
     */
    public function testCreateUserRejectsDuplicateEmail(): void
    {
        $this->fixtures->load(__DIR__ . '/Fixtures/UserManageServiceTest.yaml');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User with this email already exists.');

        $input = new CreateUserInputDto('admin@example.com', 'secret', 'admin', 'active');
        $this->service->createUser($input);
    }

    /**
     * Проверяет, что некорректный формат email отклоняется при создании пользователя.
     */
    public function testCreateUserRejectsInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email is invalid.');

        $input = new CreateUserInputDto('not-an-email', 'secret', 'admin', 'active');
        $this->service->createUser($input);
    }
}
