<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230721190311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change IP to connected_element';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera DROP ip');
        $this->addSql('ALTER TABLE connected_element ADD ip VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE modem DROP ip');
        $this->addSql('ALTER TABLE server DROP ip');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera ADD ip VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE connected_element DROP ip');
        $this->addSql('ALTER TABLE modem ADD ip VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE server ADD ip VARCHAR(255) DEFAULT NULL');
    }
}
