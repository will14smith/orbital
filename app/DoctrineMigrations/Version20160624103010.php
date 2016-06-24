<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160624103010 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE badge ADD club_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE badge ADD CONSTRAINT FK_FEF0481D61190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('CREATE INDEX IDX_FEF0481D61190A32 ON badge (club_id)');
        $this->addSql('UPDATE badge SET club_id = (SELECT id FROM club LIMIT 1)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE badge DROP FOREIGN KEY FK_FEF0481D61190A32');
        $this->addSql('DROP INDEX IDX_FEF0481D61190A32 ON badge');
        $this->addSql('ALTER TABLE badge DROP club_id');
    }
}
