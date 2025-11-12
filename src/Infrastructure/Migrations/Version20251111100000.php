<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251111100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create knight table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS knight (
            id BIGSERIAL PRIMARY KEY,
            external_id VARCHAR(36) UNIQUE NOT NULL,
            name VARCHAR(255) NOT NULL,
            strength INT NOT NULL CHECK (strength >= 0),
            weapon_power INT NOT NULL CHECK (weapon_power >= 0),
            created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS knight');
    }
}
