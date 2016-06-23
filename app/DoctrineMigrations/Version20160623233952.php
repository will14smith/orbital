<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160623233952 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE record_club (id INT AUTO_INCREMENT NOT NULL, record_id INT DEFAULT NULL, club_id INT DEFAULT NULL, activeFrom DATE DEFAULT NULL, activeUntil DATE DEFAULT NULL, INDEX IDX_5D59A9A64DFD750C (record_id), INDEX IDX_5D59A9A661190A32 (club_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE record_club ADD CONSTRAINT FK_5D59A9A64DFD750C FOREIGN KEY (record_id) REFERENCES record (id)');
        $this->addSql('ALTER TABLE record_club ADD CONSTRAINT FK_5D59A9A661190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('INSERT INTO record_club (record_id, club_id) SELECT record.id as record_id, club.id as club_id FROM record, club');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE record_club');
    }
}
