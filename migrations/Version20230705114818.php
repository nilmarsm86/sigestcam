<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705114818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE05B20A81ED');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424FB20A81ED');
        $this->addSql('ALTER TABLE server DROP FOREIGN KEY FK_5A6DD5F6B20A81ED');
        $this->addSql('CREATE TABLE structured_cable (id INT AUTO_INCREMENT NOT NULL, physical_address VARCHAR(255) NOT NULL, point VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, feeder_cable VARCHAR(255) NOT NULL, pair VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE estructured_cable');
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE05B20A81ED FOREIGN KEY (structured_cable_id) REFERENCES structured_cable (id)');
        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424FB20A81ED FOREIGN KEY (structured_cable_id) REFERENCES structured_cable (id)');
        $this->addSql('ALTER TABLE report CHANGE equipment_id equipment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F6B20A81ED FOREIGN KEY (structured_cable_id) REFERENCES structured_cable (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE05B20A81ED');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424FB20A81ED');
        $this->addSql('ALTER TABLE server DROP FOREIGN KEY FK_5A6DD5F6B20A81ED');
        $this->addSql('CREATE TABLE estructured_cable (id INT AUTO_INCREMENT NOT NULL, physical_address VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, point VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, feeder_cable VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, pair VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE structured_cable');
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE05B20A81ED FOREIGN KEY (structured_cable_id) REFERENCES estructured_cable (id)');

        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424FB20A81ED FOREIGN KEY (structured_cable_id) REFERENCES estructured_cable (id)');
        $this->addSql('ALTER TABLE report CHANGE equipment_id equipment_id INT NOT NULL');

        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F6B20A81ED FOREIGN KEY (structured_cable_id) REFERENCES estructured_cable (id)');
    }
}
