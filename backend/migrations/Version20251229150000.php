<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251229150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users, clients, client_accounts, services, invoices, invoice_items tables';
    }

    public function up(Schema $schema): void
    {
        // users
        $queries[] = <<<SQL
CREATE TABLE users (
    id SERIAL NOT NULL, 
    email VARCHAR(180) NOT NULL, 
    status VARCHAR(20) NOT NULL, 
    role VARCHAR(50) NOT NULL, 
    password_hash VARCHAR(255) NOT NULL, 
    password_reset_token VARCHAR(255) DEFAULT NULL, 
    password_reset_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
PRIMARY KEY(id)
)
SQL;
        $queries[] = <<<SQL
CREATE UNIQUE INDEX uniq_users_email ON users (email)
SQL;

        // clients
        $queries[] = <<<SQL
CREATE TABLE clients (
    id SERIAL NOT NULL, 
    user_id INT NOT NULL, 
    name VARCHAR(255) NOT NULL, 
    legal_address TEXT DEFAULT NULL, 
    country_code VARCHAR(2) DEFAULT NULL, 
    tax_id VARCHAR(64) DEFAULT NULL, 
    tax_kpp VARCHAR(64) DEFAULT NULL, 
    registration_number VARCHAR(64) DEFAULT NULL, 
    legal_details JSONB DEFAULT NULL, 
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
    deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
PRIMARY KEY(id)
)
SQL;
        $queries[] = <<<SQL
CREATE INDEX idx_clients_user ON clients (user_id)
SQL;
        $queries[] = <<<SQL
ALTER TABLE clients 
    ADD CONSTRAINT fk_clients_user 
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
SQL;

        // client_accounts
        $queries[] = <<<SQL
CREATE TABLE client_accounts (
    id SERIAL NOT NULL, 
    client_id INT NOT NULL, 
    type VARCHAR(20) NOT NULL, 
    label VARCHAR(255) DEFAULT NULL,
     currency_code VARCHAR(3) DEFAULT NULL, 
     details JSONB DEFAULT NULL, 
     is_default BOOLEAN NOT NULL, 
     created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
     updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
PRIMARY KEY(id)
)
SQL;
        $queries[] = <<<SQL
CREATE INDEX idx_client_accounts_client ON client_accounts (client_id)
SQL;
        $queries[] = <<<SQL
ALTER TABLE client_accounts 
    ADD CONSTRAINT fk_client_accounts_client 
        FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE
SQL;

        $queries[] = <<<SQL
CREATE TABLE services (
    id SERIAL NOT NULL, 
    user_id INT NOT NULL, 
    name_ru VARCHAR(255) DEFAULT NULL, 
    name_en VARCHAR(255) DEFAULT NULL, 
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
    deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
    PRIMARY KEY(id)
)
SQL;
        $queries[] = <<<SQL
CREATE INDEX idx_services_user ON services (user_id)
SQL;
        $queries[] = <<<SQL
ALTER TABLE services 
    ADD CONSTRAINT fk_services_user 
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
SQL;

        $queries[] = <<<SQL
CREATE TABLE invoices (
    id SERIAL NOT NULL, 
    user_id INT NOT NULL, 
    client_id INT NOT NULL, 
    language VARCHAR(5) NOT NULL, 
    invoice_number VARCHAR(64) NOT NULL, 
    currency VARCHAR(3) NOT NULL, 
    issued_at DATE NOT NULL, 
    vat_mode VARCHAR(20) NOT NULL, 
    status VARCHAR(20) NOT NULL, 
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
    deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
PRIMARY KEY(id)
)
SQL;
        $queries[] = <<<SQL
CREATE UNIQUE INDEX uniq_invoices_number ON invoices (invoice_number)
SQL;
        $queries[] = <<<SQL
CREATE INDEX idx_invoices_user ON invoices (user_id)
SQL;
        $queries[] = <<<SQL
CREATE INDEX idx_invoices_client ON invoices (client_id)
SQL;
        $queries[] = <<<SQL
ALTER TABLE invoices 
    ADD CONSTRAINT fk_invoices_user 
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
SQL;
        $queries[] = <<<SQL
ALTER TABLE invoices 
    ADD CONSTRAINT fk_invoices_client 
        FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE
SQL;

        $queries[] = <<<SQL
CREATE TABLE invoice_items (
    id SERIAL NOT NULL, 
    invoice_id INT NOT NULL, 
    service_id INT DEFAULT NULL, 
    title VARCHAR(255) NOT NULL, 
    hours DOUBLE PRECISION NOT NULL, 
    unit_price INT NOT NULL, 
PRIMARY KEY(id)
)
SQL;
        $queries[] = <<<SQL
CREATE INDEX idx_invoice_items_invoice ON invoice_items (invoice_id)
SQL;
        $queries[] = <<<SQL
CREATE INDEX idx_invoice_items_service ON invoice_items (service_id)
SQL;
        $queries[] = <<<SQL
ALTER TABLE invoice_items 
    ADD CONSTRAINT fk_invoice_items_invoice 
        FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE
SQL;
        $queries[] = <<<SQL
ALTER TABLE invoice_items 
    ADD CONSTRAINT fk_invoice_items_service 
        FOREIGN KEY (service_id) REFERENCES services (id) ON DELETE SET NULL
SQL;

        foreach ($queries as $query) {
            $this->addSql($query);
        }
    }

    public function down(Schema $schema): void
    {
        $queries[] = <<<SQL
DROP TABLE invoice_items
SQL;
        $queries[] = <<<SQL
        DROP TABLE invoices
SQL;
        $queries[] = <<<SQL
        DROP TABLE services
SQL;
        $queries[] = <<<SQL
        DROP TABLE client_accounts
SQL;
        $queries[] = <<<SQL
        DROP TABLE clients
SQL;
        $queries[] = <<<SQL
        DROP TABLE users
SQL;

        foreach ($queries as $query) {
            $this->addSql($query);
        }
    }
}
