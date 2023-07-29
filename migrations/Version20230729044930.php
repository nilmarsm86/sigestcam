<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230729044930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camera (id INT NOT NULL, modem_id INT DEFAULT NULL, structured_cable_id INT DEFAULT NULL, user VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, electronic_serial VARCHAR(255) DEFAULT NULL, INDEX IDX_3B1CEE05C1C9D082 (modem_id), UNIQUE INDEX UNIQ_3B1CEE05B20A81ED (structured_cable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, msam_id INT NOT NULL, name VARCHAR(255) NOT NULL, slot INT NOT NULL, INDEX IDX_161498D3EA5E035 (msam_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commutator (id INT NOT NULL, ports_amount INT NOT NULL, gateway VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, municipality_id INT NOT NULL, port_id INT DEFAULT NULL, brand VARCHAR(255) DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, physical_address VARCHAR(255) NOT NULL, physical_serial VARCHAR(255) DEFAULT NULL, model VARCHAR(255) DEFAULT NULL, inventory VARCHAR(255) DEFAULT NULL, contic VARCHAR(255) DEFAULT NULL, state INT NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_D338D583AE6F181C (municipality_id), UNIQUE INDEX UNIQ_D338D58376E92A9C (port_id), UNIQUE INDEX ip (ip), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modem (id INT NOT NULL, slave_modem_id INT DEFAULT NULL, structured_cable_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A092424F48527A53 (slave_modem_id), UNIQUE INDEX UNIQ_A092424FB20A81ED (structured_cable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE msam (id INT NOT NULL, slot_amount INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE municipality (id INT AUTO_INCREMENT NOT NULL, province_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C6F56628E946114A (province_id), UNIQUE INDEX name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organ (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE port (id INT AUTO_INCREMENT NOT NULL, commutator_id INT DEFAULT NULL, card_id INT DEFAULT NULL, equipment_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, speed DOUBLE PRECISION NOT NULL, connection_type VARCHAR(255) DEFAULT NULL, state INT NOT NULL, INDEX IDX_43915DCC35DAD24B (commutator_id), INDEX IDX_43915DCC4ACC9A20 (card_id), UNIQUE INDEX UNIQ_43915DCC517FE9FE (equipment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, equipment_id INT NOT NULL, boss_id INT NOT NULL, management_officer_id INT NOT NULL, organ_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, specialty VARCHAR(255) NOT NULL, entry_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', close_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', type INT NOT NULL, interruption_reason LONGTEXT DEFAULT NULL, priority INT NOT NULL, flaw LONGTEXT DEFAULT NULL, observation LONGTEXT DEFAULT NULL, solution LONGTEXT DEFAULT NULL, unit VARCHAR(255) NOT NULL, state INT NOT NULL, aim INT NOT NULL, INDEX IDX_C42F7784517FE9FE (equipment_id), INDEX IDX_C42F7784261FB672 (boss_id), INDEX IDX_C42F77842972046C (management_officer_id), INDEX IDX_C42F7784E4445171 (organ_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE server (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE structured_cable (id INT AUTO_INCREMENT NOT NULL, physical_address VARCHAR(255) NOT NULL, point VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, feeder_cable VARCHAR(255) NOT NULL, pair VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, state INT NOT NULL, UNIQUE INDEX username (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE05C1C9D082 FOREIGN KEY (modem_id) REFERENCES modem (id)');
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE05B20A81ED FOREIGN KEY (structured_cable_id) REFERENCES structured_cable (id)');
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE05BF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3EA5E035 FOREIGN KEY (msam_id) REFERENCES msam (id)');
        $this->addSql('ALTER TABLE commutator ADD CONSTRAINT FK_20BEECD0BF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D583AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id)');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D58376E92A9C FOREIGN KEY (port_id) REFERENCES port (id)');
        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424F48527A53 FOREIGN KEY (slave_modem_id) REFERENCES modem (id)');
        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424FB20A81ED FOREIGN KEY (structured_cable_id) REFERENCES structured_cable (id)');
        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424FBF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE msam ADD CONSTRAINT FK_F15A61C8BF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE municipality ADD CONSTRAINT FK_C6F56628E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE port ADD CONSTRAINT FK_43915DCC35DAD24B FOREIGN KEY (commutator_id) REFERENCES commutator (id)');
        $this->addSql('ALTER TABLE port ADD CONSTRAINT FK_43915DCC4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE port ADD CONSTRAINT FK_43915DCC517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784261FB672 FOREIGN KEY (boss_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77842972046C FOREIGN KEY (management_officer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784E4445171 FOREIGN KEY (organ_id) REFERENCES organ (id)');
        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F6BF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE05C1C9D082');
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE05B20A81ED');
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE05BF396750');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3EA5E035');
        $this->addSql('ALTER TABLE commutator DROP FOREIGN KEY FK_20BEECD0BF396750');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D583AE6F181C');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D58376E92A9C');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424F48527A53');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424FB20A81ED');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424FBF396750');
        $this->addSql('ALTER TABLE msam DROP FOREIGN KEY FK_F15A61C8BF396750');
        $this->addSql('ALTER TABLE municipality DROP FOREIGN KEY FK_C6F56628E946114A');
        $this->addSql('ALTER TABLE port DROP FOREIGN KEY FK_43915DCC35DAD24B');
        $this->addSql('ALTER TABLE port DROP FOREIGN KEY FK_43915DCC4ACC9A20');
        $this->addSql('ALTER TABLE port DROP FOREIGN KEY FK_43915DCC517FE9FE');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784517FE9FE');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784261FB672');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77842972046C');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784E4445171');
        $this->addSql('ALTER TABLE server DROP FOREIGN KEY FK_5A6DD5F6BF396750');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
        $this->addSql('DROP TABLE camera');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE commutator');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE modem');
        $this->addSql('DROP TABLE msam');
        $this->addSql('DROP TABLE municipality');
        $this->addSql('DROP TABLE organ');
        $this->addSql('DROP TABLE port');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE server');
        $this->addSql('DROP TABLE structured_cable');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_role');
    }
}
