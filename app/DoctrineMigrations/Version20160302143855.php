<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160302143855 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE record_round (id INT AUTO_INCREMENT NOT NULL, record_id INT DEFAULT NULL, round_id INT DEFAULT NULL, count INT NOT NULL, skill VARCHAR(255) DEFAULT NULL, bowtype VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, INDEX IDX_44B54B684DFD750C (record_id), INDEX IDX_44B54B68A6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE record_round ADD CONSTRAINT FK_44B54B684DFD750C FOREIGN KEY (record_id) REFERENCES record (id)');
        $this->addSql('ALTER TABLE record_round ADD CONSTRAINT FK_44B54B68A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        // migrate data
        $this->addSql('INSERT INTO record_round (record_id, round_id, `count`, skill, bowtype, gender) SELECT id, round_id, 1 as `count`, skill, bowtype, gender FROM record');

        $this->addSql('ALTER TABLE record DROP FOREIGN KEY FK_9B349F91A6005CA0');
        $this->addSql('DROP INDEX IDX_9B349F91A6005CA0 ON record');
        $this->addSql('ALTER TABLE record DROP round_id, DROP skill, DROP gender, DROP bowtype');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE record_round');
        $this->addSql('ALTER TABLE record ADD round_id INT DEFAULT NULL, ADD skill VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD gender VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD bowtype VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE record ADD CONSTRAINT FK_9B349F91A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('CREATE INDEX IDX_9B349F91A6005CA0 ON record (round_id)');
    }
}
