<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230724163832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment CHANGE state state INT NOT NULL');
        $this->addSql('ALTER TABLE port CHANGE state state INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE state state INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment CHANGE state state VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE port CHANGE state state VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE state state VARCHAR(255) NOT NULL');
    }
}
