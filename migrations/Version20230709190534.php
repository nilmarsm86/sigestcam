<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230709190534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera ADD electronic_serial VARCHAR(255) DEFAULT NULL, CHANGE serial fisical_serial VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commutator ADD gateway VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE modem CHANGE serial fisical_serial VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE server CHANGE serial fisical_serial VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD active TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera DROP electronic_serial, CHANGE fisical_serial serial VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commutator DROP gateway');
        $this->addSql('ALTER TABLE modem CHANGE fisical_serial serial VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE server CHANGE fisical_serial serial VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP active');
    }
}
