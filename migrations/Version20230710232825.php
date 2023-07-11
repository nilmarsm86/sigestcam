<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230710232825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera DROP fisical_serial');
        $this->addSql('ALTER TABLE equipment ADD physical_serial VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE modem DROP fisical_serial');
        $this->addSql('ALTER TABLE server DROP fisical_serial');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera ADD fisical_serial VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE equipment DROP physical_serial');
        $this->addSql('ALTER TABLE modem ADD fisical_serial VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE server ADD fisical_serial VARCHAR(255) NOT NULL');
    }
}
