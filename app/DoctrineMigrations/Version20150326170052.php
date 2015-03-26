<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150326170052 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE score_proof (id INT AUTO_INCREMENT NOT NULL, score_id INT DEFAULT NULL, person_id INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, INDEX IDX_42B58C5512EB0A51 (score_id), INDEX IDX_42B58C55217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE score_proof ADD CONSTRAINT FK_42B58C5512EB0A51 FOREIGN KEY (score_id) REFERENCES score (id)');
        $this->addSql('ALTER TABLE score_proof ADD CONSTRAINT FK_42B58C55217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE score_proof');
    }
}
