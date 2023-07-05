<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705033811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE estructured_cable (id INT AUTO_INCREMENT NOT NULL, physical_address VARCHAR(255) NOT NULL, point VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, feeder_cable VARCHAR(255) NOT NULL, pair VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organ (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE server (id INT NOT NULL, structured_cable_id INT DEFAULT NULL, model VARCHAR(255) NOT NULL, serial VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, inventory VARCHAR(255) NOT NULL, contic VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5A6DD5F6B20A81ED (structured_cable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F6B20A81ED FOREIGN KEY (structured_cable_id) REFERENCES estructured_cable (id)');
        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F6BF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE camera ADD structured_cable_id INT DEFAULT NULL, ADD contic VARCHAR(255) NOT NULL, DROP structured_cable');
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE05B20A81ED FOREIGN KEY (structured_cable_id) REFERENCES estructured_cable (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B1CEE05B20A81ED ON camera (structured_cable_id)');
        $this->addSql('ALTER TABLE modem ADD structured_cable_id INT DEFAULT NULL, ADD contic VARCHAR(255) NOT NULL, DROP structured_cable');
        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424FB20A81ED FOREIGN KEY (structured_cable_id) REFERENCES estructured_cable (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A092424FB20A81ED ON modem (structured_cable_id)');
        $this->addSql('ALTER TABLE msam ADD slot_amount INT NOT NULL');
        $this->addSql('ALTER TABLE municipality ADD province_id INT NOT NULL');
        $this->addSql('ALTER TABLE municipality ADD CONSTRAINT FK_C6F56628E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('CREATE INDEX IDX_C6F56628E946114A ON municipality (province_id)');
        $this->addSql('ALTER TABLE port CHANGE number number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE report ADD organ_id INT DEFAULT NULL, ADD aim VARCHAR(255) NOT NULL, DROP organ');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784E4445171 FOREIGN KEY (organ_id) REFERENCES organ (id)');
        $this->addSql('CREATE INDEX IDX_C42F7784E4445171 ON report (organ_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE05B20A81ED');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424FB20A81ED');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784E4445171');
        $this->addSql('ALTER TABLE municipality DROP FOREIGN KEY FK_C6F56628E946114A');
        $this->addSql('ALTER TABLE server DROP FOREIGN KEY FK_5A6DD5F6B20A81ED');
        $this->addSql('ALTER TABLE server DROP FOREIGN KEY FK_5A6DD5F6BF396750');
        $this->addSql('DROP TABLE estructured_cable');
        $this->addSql('DROP TABLE organ');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE server');
        $this->addSql('DROP INDEX UNIQ_3B1CEE05B20A81ED ON camera');
        $this->addSql('ALTER TABLE camera ADD structured_cable LONGTEXT NOT NULL, DROP structured_cable_id, DROP contic');
        $this->addSql('DROP INDEX UNIQ_A092424FB20A81ED ON modem');
        $this->addSql('ALTER TABLE modem ADD structured_cable LONGTEXT NOT NULL, DROP structured_cable_id, DROP contic');
        $this->addSql('ALTER TABLE msam DROP slot_amount');
        $this->addSql('DROP INDEX IDX_C6F56628E946114A ON municipality');
        $this->addSql('ALTER TABLE municipality DROP province_id');
        $this->addSql('ALTER TABLE port CHANGE number number INT NOT NULL');
        $this->addSql('DROP INDEX IDX_C42F7784E4445171 ON report');
        $this->addSql('ALTER TABLE report ADD organ VARCHAR(255) DEFAULT NULL, DROP organ_id, DROP aim');
    }
}
