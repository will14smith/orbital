<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150319160727 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE score (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, round_id INT DEFAULT NULL, skill VARCHAR(50) NOT NULL, bowtype VARCHAR(50) NOT NULL, score INT NOT NULL, golds INT NOT NULL, hits INT NOT NULL, competition TINYINT(1) NOT NULL, complete TINYINT(1) NOT NULL, date_shot DATETIME NOT NULL, date_entered DATETIME NOT NULL, date_accepted DATETIME DEFAULT NULL, INDEX IDX_32993751217BBB47 (person_id), INDEX IDX_32993751A6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE score');
    }
}
