<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160624001150 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE record_holder ADD club_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE record_holder ADD CONSTRAINT FK_B4F42EC361190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('CREATE INDEX IDX_B4F42EC361190A32 ON record_holder (club_id)');
        $this->addSql('UPDATE record_holder SET club_id = (SELECT id FROM club LIMIT 1)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE record_holder DROP FOREIGN KEY FK_B4F42EC361190A32');
        $this->addSql('DROP INDEX IDX_B4F42EC361190A32 ON record_holder');
        $this->addSql('ALTER TABLE record_holder DROP club_id');
    }
}
