<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addOption('role', null, InputOption::VALUE_OPTIONAL, 'User role', User::ROLE_ADMIN)
            ->addOption('status', null, InputOption::VALUE_OPTIONAL, 'User status', User::STATUS_ACTIVE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = (string) $input->getArgument('email');

        $existing = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing instanceof User) {
            $io->error('User with this email already exists.');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRole((string) $input->getOption('role'));
        $user->setStatus((string) $input->getOption('status'));
        $user->setPasswordHash(password_hash((string) $input->getArgument('password'), PASSWORD_DEFAULT));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('User created.');

        return Command::SUCCESS;
    }
}
