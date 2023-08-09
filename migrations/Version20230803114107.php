<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230803114107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment CHANGE state state VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE port DROP FOREIGN KEY FK_43915DCC517FE9FE');
        $this->addSql('DROP INDEX UNIQ_43915DCC517FE9FE ON port');
        $this->addSql('ALTER TABLE port DROP equipment_id, CHANGE number number INT NOT NULL, CHANGE state state VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE report CHANGE type type VARCHAR(255) NOT NULL, CHANGE priority priority VARCHAR(255) NOT NULL, CHANGE state state VARCHAR(255) NOT NULL, CHANGE aim aim VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE state state VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment CHANGE state state INT NOT NULL');
        $this->addSql('ALTER TABLE port ADD equipment_id INT DEFAULT NULL, CHANGE number number VARCHAR(255) NOT NULL, CHANGE state state INT NOT NULL');
        $this->addSql('ALTER TABLE port ADD CONSTRAINT FK_43915DCC517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_43915DCC517FE9FE ON port (equipment_id)');
        $this->addSql('ALTER TABLE report CHANGE type type INT NOT NULL, CHANGE priority priority INT NOT NULL, CHANGE state state INT NOT NULL, CHANGE aim aim INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE state state INT NOT NULL');
    }
}
