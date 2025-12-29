<?php

namespace App\Command;

use App\Entity\Client;
use App\Entity\ClientAccount;
use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:seed-dev',
    description: 'Seed development data (one user, client, service, invoice).'
)]
final class SeedDevCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        #[Autowire('%kernel.environment%')] private readonly string $environment
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Allow seeding outside dev');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->environment !== 'dev' && !$input->getOption('force')) {
            $io->error('Seeding is allowed only in dev. Use --force to override.');
            return Command::FAILURE;
        }

        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['email' => 'admin@example.com']);
        if (!$user instanceof User) {
            $user = new User();
            $user->setEmail('admin@example.com');
            $user->setRole(User::ROLE_ADMIN);
            $user->setStatus(User::STATUS_ACTIVE);
            $user->setPasswordHash(password_hash('admin123', PASSWORD_DEFAULT));
            $this->entityManager->persist($user);
        }

        $clientRepo = $this->entityManager->getRepository(Client::class);
        $client = $clientRepo->findOneBy(['name' => 'Test Client', 'user' => $user]);
        if (!$client instanceof Client) {
            $client = new Client();
            $client->setUser($user);
            $client->setName('Test Client');
            $client->setCountryCode('RU');
            $client->setTaxId('7701234567');
            $client->setTaxKpp('770101001');
            $client->setRegistrationNumber('1027700000000');
            $client->setLegalAddress('Москва, ул. Пример, 1');
            $this->entityManager->persist($client);

            $account = new ClientAccount();
            $account->setClient($client);
            $account->setType(ClientAccount::TYPE_BANK);
            $account->setLabel('Основной счет');
            $account->setCurrencyCode('RUB');
            $account->setIsDefault(true);
            $account->setDetails([
                'bank' => 'Тест Банк',
                'bik' => '044525000',
                'account' => '40702810900000000001',
                'correspondent_account' => '30101810400000000225',
                'bank_tax_id' => '7700000000',
                'bank_address' => 'Москва, ул. Банк, 1',
            ]);
            $this->entityManager->persist($account);
        }

        $serviceRepo = $this->entityManager->getRepository(Service::class);
        $service = $serviceRepo->findOneBy(['user' => $user, 'nameRu' => 'Консультации']);
        if (!$service instanceof Service) {
            $service = new Service();
            $service->setUser($user);
            $service->setNameRu('Консультации');
            $service->setNameEn('Consulting');
            $this->entityManager->persist($service);
        }

        $invoiceRepo = $this->entityManager->getRepository(Invoice::class);
        $invoice = $invoiceRepo->findOneBy(['invoiceNumber' => 'INV-0001']);
        if (!$invoice instanceof Invoice) {
            $invoice = new Invoice();
            $invoice->setUser($user);
            $invoice->setClient($client);
            $invoice->setLanguage('ru');
            $invoice->setInvoiceNumber('INV-0001');
            $invoice->setCurrency('RUB');
            $invoice->setIssuedAt(new \DateTimeImmutable('today'));
            $invoice->setVatMode(Invoice::VAT_WITHOUT);
            $invoice->setStatus(Invoice::STATUS_ISSUED);
            $this->entityManager->persist($invoice);

            $item = new InvoiceItem();
            $item->setInvoice($invoice);
            $item->setService($service);
            $item->setTitle('Консультационные услуги');
            $item->setHours(10.0);
            $item->setUnitPrice(5000);
            $this->entityManager->persist($item);
        }

        $this->entityManager->flush();

        $io->success('Seed data created.');

        return Command::SUCCESS;
    }
}
