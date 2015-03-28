<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150328004143 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE badge_holder_proof (id INT AUTO_INCREMENT NOT NULL, badge_holder_id INT DEFAULT NULL, person_id INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, INDEX IDX_6755564BEF423D44 (badge_holder_id), INDEX IDX_6755564B217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE badge_holder_proof ADD CONSTRAINT FK_6755564BEF423D44 FOREIGN KEY (badge_holder_id) REFERENCES badge_holder (id)');
        $this->addSql('ALTER TABLE badge_holder_proof ADD CONSTRAINT FK_6755564B217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE badge_holder_proof');
    }
}
