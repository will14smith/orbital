<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150328203650 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE badge_holder_proof ADD voucher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE badge_holder_proof ADD CONSTRAINT FK_6755564B28AA1B6F FOREIGN KEY (voucher_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_6755564B28AA1B6F ON badge_holder_proof (voucher_id)');
        $this->addSql('ALTER TABLE league_match_proof ADD voucher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE league_match_proof ADD CONSTRAINT FK_489F6CF228AA1B6F FOREIGN KEY (voucher_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_489F6CF228AA1B6F ON league_match_proof (voucher_id)');
        $this->addSql('ALTER TABLE score_proof ADD voucher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE score_proof ADD CONSTRAINT FK_42B58C5528AA1B6F FOREIGN KEY (voucher_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_42B58C5528AA1B6F ON score_proof (voucher_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE badge_holder_proof DROP FOREIGN KEY FK_6755564B28AA1B6F');
        $this->addSql('DROP INDEX IDX_6755564B28AA1B6F ON badge_holder_proof');
        $this->addSql('ALTER TABLE badge_holder_proof DROP voucher_id');
        $this->addSql('ALTER TABLE league_match_proof DROP FOREIGN KEY FK_489F6CF228AA1B6F');
        $this->addSql('DROP INDEX IDX_489F6CF228AA1B6F ON league_match_proof');
        $this->addSql('ALTER TABLE league_match_proof DROP voucher_id');
        $this->addSql('ALTER TABLE score_proof DROP FOREIGN KEY FK_42B58C5528AA1B6F');
        $this->addSql('DROP INDEX IDX_42B58C5528AA1B6F ON score_proof');
        $this->addSql('ALTER TABLE score_proof DROP voucher_id');
    }
}
