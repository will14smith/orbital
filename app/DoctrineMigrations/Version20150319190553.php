<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150319190553 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE record (id INT AUTO_INCREMENT NOT NULL, round_id INT DEFAULT NULL, num_holders INT NOT NULL, skill VARCHAR(255) NOT NULL, gender VARCHAR(255) DEFAULT NULL, INDEX IDX_9B349F91A6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE record_holder (id INT AUTO_INCREMENT NOT NULL, record_id INT DEFAULT NULL, person_id INT DEFAULT NULL, score_id INT DEFAULT NULL, score_value INT NOT NULL, location VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_B4F42EC34DFD750C (record_id), INDEX IDX_B4F42EC3217BBB47 (person_id), INDEX IDX_B4F42EC312EB0A51 (score_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE record ADD CONSTRAINT FK_9B349F91A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE record_holder ADD CONSTRAINT FK_B4F42EC34DFD750C FOREIGN KEY (record_id) REFERENCES record (id)');
        $this->addSql('ALTER TABLE record_holder ADD CONSTRAINT FK_B4F42EC3217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE record_holder ADD CONSTRAINT FK_B4F42EC312EB0A51 FOREIGN KEY (score_id) REFERENCES score (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE record_holder DROP FOREIGN KEY FK_B4F42EC34DFD750C');
        $this->addSql('DROP TABLE record');
        $this->addSql('DROP TABLE record_holder');
    }
}
