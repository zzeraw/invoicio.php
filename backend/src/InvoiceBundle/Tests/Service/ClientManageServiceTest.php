<?php

namespace App\InvoiceBundle\Tests\Service;

use App\InvoiceBundle\PublicInterface\ClientCreateDataInterface;
use App\InvoiceBundle\PublicInterface\ClientUpdateDataInterface;
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

        $created = $service->createForUser($userId, $this->createCreateData(
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

        $updated = $service->updateForUser($userId, $created->getId(), $this->createUpdateData(
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

        $client = $service->createForUser($userId1, $this->createCreateData(
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

        $client = $service->createForUser($userId, $this->createCreateData(
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

        $service->updateForUser($userId, $client->getId(), $this->createUpdateData(
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

    /**
     * @param array<string, mixed>|null $legalDetails
     */
    private function createCreateData(
        string $name,
        ?string $legalAddress,
        ?string $countryCode,
        ?string $taxId,
        ?string $taxKpp,
        ?string $registrationNumber,
        ?array $legalDetails
    ): ClientCreateDataInterface {
        return new class(
            $name,
            $legalAddress,
            $countryCode,
            $taxId,
            $taxKpp,
            $registrationNumber,
            $legalDetails
        ) implements ClientCreateDataInterface {
            /**
             * @param array<string, mixed>|null $legalDetails
             */
            public function __construct(
                private string $name,
                private ?string $legalAddress,
                private ?string $countryCode,
                private ?string $taxId,
                private ?string $taxKpp,
                private ?string $registrationNumber,
                private ?array $legalDetails
            ) {
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getLegalAddress(): ?string
            {
                return $this->legalAddress;
            }

            public function getCountryCode(): ?string
            {
                return $this->countryCode;
            }

            public function getTaxId(): ?string
            {
                return $this->taxId;
            }

            public function getTaxKpp(): ?string
            {
                return $this->taxKpp;
            }

            public function getRegistrationNumber(): ?string
            {
                return $this->registrationNumber;
            }

            /**
             * @return array<string, mixed>|null
             */
            public function getLegalDetails(): ?array
            {
                return $this->legalDetails;
            }
        };
    }

    /**
     * @param array<string, mixed>|null $legalDetails
     * @param array<string, bool> $fields
     */
    private function createUpdateData(
        ?string $name,
        ?string $legalAddress,
        ?string $countryCode,
        ?string $taxId,
        ?string $taxKpp,
        ?string $registrationNumber,
        ?array $legalDetails,
        array $fields
    ): ClientUpdateDataInterface {
        return new class(
            $name,
            $legalAddress,
            $countryCode,
            $taxId,
            $taxKpp,
            $registrationNumber,
            $legalDetails,
            $fields
        ) implements ClientUpdateDataInterface {
            /**
             * @param array<string, mixed>|null $legalDetails
             * @param array<string, bool> $fields
             */
            public function __construct(
                private ?string $name,
                private ?string $legalAddress,
                private ?string $countryCode,
                private ?string $taxId,
                private ?string $taxKpp,
                private ?string $registrationNumber,
                private ?array $legalDetails,
                private array $fields
            ) {
            }

            public function getName(): ?string
            {
                return $this->name;
            }

            public function getLegalAddress(): ?string
            {
                return $this->legalAddress;
            }

            public function getCountryCode(): ?string
            {
                return $this->countryCode;
            }

            public function getTaxId(): ?string
            {
                return $this->taxId;
            }

            public function getTaxKpp(): ?string
            {
                return $this->taxKpp;
            }

            public function getRegistrationNumber(): ?string
            {
                return $this->registrationNumber;
            }

            /**
             * @return array<string, mixed>|null
             */
            public function getLegalDetails(): ?array
            {
                return $this->legalDetails;
            }

            /**
             * @return array<string, bool>
             */
            public function getFields(): array
            {
                return $this->fields;
            }
        };
    }
}
