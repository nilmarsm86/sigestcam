<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904155256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commutator ADD master_commutator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commutator ADD CONSTRAINT FK_20BEECD0B84F07C1 FOREIGN KEY (master_commutator_id) REFERENCES commutator (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_20BEECD0B84F07C1 ON commutator (master_commutator_id)');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424F48527A53');
        $this->addSql('DROP INDEX UNIQ_A092424F48527A53 ON modem');
        $this->addSql('ALTER TABLE modem CHANGE slave_modem_id master_modem_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424F827C1E05 FOREIGN KEY (master_modem_id) REFERENCES modem (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A092424F827C1E05 ON modem (master_modem_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commutator DROP FOREIGN KEY FK_20BEECD0B84F07C1');
        $this->addSql('DROP INDEX UNIQ_20BEECD0B84F07C1 ON commutator');
        $this->addSql('ALTER TABLE commutator DROP master_commutator_id');
        $this->addSql('ALTER TABLE modem DROP FOREIGN KEY FK_A092424F827C1E05');
        $this->addSql('DROP INDEX UNIQ_A092424F827C1E05 ON modem');
        $this->addSql('ALTER TABLE modem CHANGE master_modem_id slave_modem_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modem ADD CONSTRAINT FK_A092424F48527A53 FOREIGN KEY (slave_modem_id) REFERENCES modem (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A092424F48527A53 ON modem (slave_modem_id)');
    }
}
