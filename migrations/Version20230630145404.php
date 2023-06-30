<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230630145404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camera (id INT NOT NULL, modem_id INT DEFAULT NULL, user VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, serial VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, structured_cable LONGTEXT NOT NULL, INDEX IDX_3B1CEE05C1C9D082 (modem_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, msam_id INT NOT NULL, name VARCHAR(255) NOT NULL, slot INT NOT NULL, INDEX IDX_161498D3EA5E035 (msam_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commutator (id INT NOT NULL, ip VARCHAR(255) NOT NULL, ports_amount INT NOT NULL, UNIQUE INDEX ip (ip), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE connected_element (id INT NOT NULL, brand VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, municipality_id INT NOT NULL, physical_address VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_D338D583AE6F181C (municipality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modem (id INT NOT NULL, slave_modem_id INT DEFAULT NULL, model VARCHAR(255) NOT NULL, serial VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, structured_cable LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_A092424F48527A53 (slave_modem_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE msam (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE municipality (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE port (id INT AUTO_INCREMENT NOT NULL, commutator_id INT DEFAULT NULL, card_id INT DEFAULT NULL, connected_element_id INT DEFAULT NULL, number INT NOT NULL, speed DOUBLE PRECISION NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_43915DCC35DAD24B (commutator_id), INDEX IDX_43915DCC4ACC9A20 (card_id), UNIQUE INDEX UNIQ_43915DCCA78F4936 (connected_element_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, equipment_id INT NOT NULL, boss_id INT NOT NULL, management_officer_id INT NOT NULL, number VARCHAR(255) NOT NULL, specialty VARCHAR(255) NOT NULL, entry_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', close_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', type VARCHAR(255) NOT NULL, interruption_reason LONGTEXT DEFAULT NULL, priority VARCHAR(255) NOT NULL, flaw LONGTEXT DEFAULT NULL, observation LONGTEXT DEFAULT NULL, solution LONGTEXT DEFAULT NULL, organ VARCHAR(255) DEFAULT NULL, unit VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_C42F7784517FE9FE (equipment_id), INDEX IDX_C42F7784261FB672 (boss_id), INDEX IDX_C42F77842972046C (management_officer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rol (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_rol (user_id INT NOT NULL, rol_id INT NOT NULL, INDEX IDX_E5435EBCA76ED395 (user_id), INDEX IDX_E5435EBC4BAB96C (rol_id), PRIMARY KEY(user_id, rol_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE05C1C9D082 FOREIGN KEY (modem_id) REFERENCES modem (id)');
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE05BF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3EA5E035 FOREIGN KEY (msam_id) REFERENCES msam (id)');
        $this->addSql('ALTER TABLE commutator ADD CONSTRAINT FK_20BEECD0BF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE connected_element ADD CONSTRAINT FK_D2B019C0BF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D583AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id)');
        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424F48527A53 FOREIGN KEY (slave_modem_id) REFERENCES modem (id)');
        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424FBF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE msam ADD CONSTRAINT FK_F15A61C8BF396750 FOREIGN KEY (id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE port ADD CONSTRAINT FK_43915DCC35DAD24B FOREIGN KEY (commutator_id) REFERENCES commutator (id)');
        $this->addSql('ALTER TABLE port ADD CONSTRAINT FK_43915DCC4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE port ADD CONSTRAINT FK_43915DCCA78F4936 FOREIGN KEY (connected_element_id) REFERENCES connected_element (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784261FB672 FOREIGN KEY (boss_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77842972046C FOREIGN KEY (management_officer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_rol ADD CONSTRAINT FK_E5435EBCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_rol ADD CONSTRAINT FK_E5435EBC4BAB96C FOREIGN KEY (rol_id) REFERENCES rol (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE05C1C9D082');
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE05BF396750');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3EA5E035');
        $this->addSql('ALTER TABLE commutator DROP FOREIGN KEY FK_20BEECD0BF396750');
        $this->addSql('ALTER TABLE connected_element DROP FOREIGN KEY FK_D2B019C0BF396750');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D583AE6F181C');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424F48527A53');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424FBF396750');
        $this->addSql('ALTER TABLE msam DROP FOREIGN KEY FK_F15A61C8BF396750');
        $this->addSql('ALTER TABLE port DROP FOREIGN KEY FK_43915DCC35DAD24B');
        $this->addSql('ALTER TABLE port DROP FOREIGN KEY FK_43915DCC4ACC9A20');
        $this->addSql('ALTER TABLE port DROP FOREIGN KEY FK_43915DCCA78F4936');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784517FE9FE');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784261FB672');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77842972046C');
        $this->addSql('ALTER TABLE user_rol DROP FOREIGN KEY FK_E5435EBCA76ED395');
        $this->addSql('ALTER TABLE user_rol DROP FOREIGN KEY FK_E5435EBC4BAB96C');
        $this->addSql('DROP TABLE camera');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE commutator');
        $this->addSql('DROP TABLE connected_element');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE modem');
        $this->addSql('DROP TABLE msam');
        $this->addSql('DROP TABLE municipality');
        $this->addSql('DROP TABLE port');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE rol');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_rol');
    }
}
