<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150328215827 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competition_entry ADD round_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA6A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('CREATE INDEX IDX_D896AFA6A6005CA0 ON competition_entry (round_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competition_entry DROP FOREIGN KEY FK_D896AFA6A6005CA0');
        $this->addSql('DROP INDEX IDX_D896AFA6A6005CA0 ON competition_entry');
        $this->addSql('ALTER TABLE competition_entry DROP round_id');
    }
}
