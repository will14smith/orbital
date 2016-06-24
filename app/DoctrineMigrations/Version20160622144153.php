<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160622144153 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE score ADD club_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_3299375161190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('CREATE INDEX IDX_3299375161190A32 ON score (club_id)');
        $this->addSql('UPDATE score SET club_id = (SELECT club_id FROM person WHERE person.id = score.person_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_3299375161190A32');
        $this->addSql('DROP INDEX IDX_3299375161190A32 ON score');
        $this->addSql('ALTER TABLE score DROP club_id');
    }
}
