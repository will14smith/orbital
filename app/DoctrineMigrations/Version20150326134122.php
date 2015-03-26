<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150326134122 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person_handicap ADD score_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person_handicap ADD CONSTRAINT FK_A1BBDF8C12EB0A51 FOREIGN KEY (score_id) REFERENCES score (id)');
        $this->addSql('CREATE INDEX IDX_A1BBDF8C12EB0A51 ON person_handicap (score_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person_handicap DROP FOREIGN KEY FK_A1BBDF8C12EB0A51');
        $this->addSql('DROP INDEX IDX_A1BBDF8C12EB0A51 ON person_handicap');
        $this->addSql('ALTER TABLE person_handicap DROP score_id');
    }
}
