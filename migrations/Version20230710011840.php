<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230710011840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera CHANGE ip ip VARCHAR(255) DEFAULT NULL, CHANGE inventory inventory VARCHAR(255) DEFAULT NULL, CHANGE contic contic VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE modem CHANGE ip ip VARCHAR(255) DEFAULT NULL, CHANGE inventory inventory VARCHAR(255) DEFAULT NULL, CHANGE contic contic VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE server CHANGE ip ip VARCHAR(255) DEFAULT NULL, CHANGE inventory inventory VARCHAR(255) DEFAULT NULL, CHANGE contic contic VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera CHANGE ip ip VARCHAR(255) NOT NULL, CHANGE inventory inventory VARCHAR(255) NOT NULL, CHANGE contic contic VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE modem CHANGE ip ip VARCHAR(255) NOT NULL, CHANGE inventory inventory VARCHAR(255) NOT NULL, CHANGE contic contic VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE server CHANGE ip ip VARCHAR(255) NOT NULL, CHANGE inventory inventory VARCHAR(255) NOT NULL, CHANGE contic contic VARCHAR(255) NOT NULL');
    }
}
