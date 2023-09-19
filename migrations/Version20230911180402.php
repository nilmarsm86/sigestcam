<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230911180402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commutator DROP INDEX UNIQ_20BEECD0B84F07C1, ADD INDEX IDX_20BEECD0B84F07C1 (master_commutator_id)');
        $this->addSql('ALTER TABLE modem DROP INDEX UNIQ_A092424F827C1E05, ADD INDEX IDX_A092424F827C1E05 (master_modem_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commutator DROP INDEX IDX_20BEECD0B84F07C1, ADD UNIQUE INDEX UNIQ_20BEECD0B84F07C1 (master_commutator_id)');
        $this->addSql('ALTER TABLE modem DROP INDEX IDX_A092424F827C1E05, ADD UNIQUE INDEX UNIQ_A092424F827C1E05 (master_modem_id)');
    }
}
