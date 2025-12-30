<?php

namespace App\UserBundle\Command;

use App\UserBundle\Dto\CreateUserInputDto;
use App\UserBundle\Enum\UserRoleEnum;
use App\UserBundle\Enum\UserStatusEnum;
use App\UserBundle\Service\UserManageService;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create a user (admin by default) with a hashed password.'
)]
final class CreateUserCommand extends Command
{
    // Example: php bin/console app:user:create admin@example.com secret --role=admin --status=active
    public function __construct(
        private readonly UserManageService $userManageService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addOption('role', null, InputOption::VALUE_OPTIONAL, 'User role', UserRoleEnum::ADMIN->value)
            ->addOption('status', null, InputOption::VALUE_OPTIONAL, 'User status', UserStatusEnum::ACTIVE->value);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $role = UserRoleEnum::fromString($this->getStringOption($input, 'role'));
        $status = UserStatusEnum::fromString($this->getStringOption($input, 'status'));

        $createUserInput = new CreateUserInputDto(
            $this->getStringArgument($input, 'email'),
            $this->getStringArgument($input, 'password'),
            $role,
            $status
        );

        try {
            $userId = $this->userManageService->createUser($createUserInput);
        } catch (InvalidArgumentException | RuntimeException $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }

        $io->success(sprintf('User created (id: %d).', $userId));

        return Command::SUCCESS;
    }

    private function getStringArgument(InputInterface $input, string $name): string
    {
        $value = $input->getArgument($name);
        if (!is_string($value)) {
            throw new InvalidArgumentException(sprintf('Argument "%s" must be a string.', $name));
        }

        return $value;
    }

    private function getStringOption(InputInterface $input, string $name): string
    {
        $value = $input->getOption($name);
        if (!is_string($value)) {
            throw new InvalidArgumentException(sprintf('Option "%s" must be a string.', $name));
        }

        return $value;
    }
}
