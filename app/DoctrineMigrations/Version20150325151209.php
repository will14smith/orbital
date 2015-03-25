<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150325151209 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE record_holder_person (id INT AUTO_INCREMENT NOT NULL, record_holder_id INT DEFAULT NULL, person_id INT DEFAULT NULL, score_id INT DEFAULT NULL, score_value INT NOT NULL, INDEX IDX_2218F8F5447FD2B4 (record_holder_id), INDEX IDX_2218F8F5217BBB47 (person_id), INDEX IDX_2218F8F512EB0A51 (score_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE record_holder_person ADD CONSTRAINT FK_2218F8F5447FD2B4 FOREIGN KEY (record_holder_id) REFERENCES record_holder (id)');
        $this->addSql('ALTER TABLE record_holder_person ADD CONSTRAINT FK_2218F8F5217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE record_holder_person ADD CONSTRAINT FK_2218F8F512EB0A51 FOREIGN KEY (score_id) REFERENCES score (id)');
        $this->addSql('ALTER TABLE record_holder DROP FOREIGN KEY FK_B4F42EC312EB0A51');
        $this->addSql('ALTER TABLE record_holder DROP FOREIGN KEY FK_B4F42EC3217BBB47');
        $this->addSql('DROP INDEX IDX_B4F42EC3217BBB47 ON record_holder');
        $this->addSql('DROP INDEX IDX_B4F42EC312EB0A51 ON record_holder');
        $this->addSql('ALTER TABLE record_holder DROP score_id, DROP person_id, CHANGE score_value score INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE record_holder_person');
        $this->addSql('ALTER TABLE record_holder ADD score_id INT DEFAULT NULL, ADD person_id INT DEFAULT NULL, CHANGE score score_value INT NOT NULL');
        $this->addSql('ALTER TABLE record_holder ADD CONSTRAINT FK_B4F42EC312EB0A51 FOREIGN KEY (score_id) REFERENCES score (id)');
        $this->addSql('ALTER TABLE record_holder ADD CONSTRAINT FK_B4F42EC3217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_B4F42EC3217BBB47 ON record_holder (person_id)');
        $this->addSql('CREATE INDEX IDX_B4F42EC312EB0A51 ON record_holder (score_id)');
    }
}
