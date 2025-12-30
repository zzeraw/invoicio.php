<?php

namespace App\UserBundle\Command;

use App\UserBundle\Dto\CreateUserInputDto;
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
            ->addOption('role', null, InputOption::VALUE_OPTIONAL, 'User role', UserManageService::DEFAULT_ROLE)
            ->addOption('status', null, InputOption::VALUE_OPTIONAL, 'User status', UserManageService::DEFAULT_STATUS);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $createUserInput = new CreateUserInputDto(
            (string) $input->getArgument('email'),
            (string) $input->getArgument('password'),
            (string) $input->getOption('role'),
            (string) $input->getOption('status')
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
}
