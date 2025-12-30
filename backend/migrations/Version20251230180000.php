<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251230180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create api_tokens table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
CREATE TABLE api_tokens (
    id SERIAL NOT NULL,
    token VARCHAR(64) NOT NULL,
    label VARCHAR(255) DEFAULT NULL,
    is_active BOOLEAN NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
)
SQL
        );
        $this->addSql('CREATE UNIQUE INDEX uniq_api_tokens_token ON api_tokens (token)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE api_tokens');
    }
}
