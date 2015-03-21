<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150321002245 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE badge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, description VARCHAR(255) NOT NULL, algo_name VARCHAR(20) DEFAULT NULL, category VARCHAR(20) NOT NULL, multiple TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE badge_holder (id INT AUTO_INCREMENT NOT NULL, badge_id INT DEFAULT NULL, person_id INT DEFAULT NULL, date_awarded DATE NOT NULL, date_confirmed DATE DEFAULT NULL, date_made DATE DEFAULT NULL, date_delivered DATE DEFAULT NULL, INDEX IDX_E414CBF8F7A2C2FC (badge_id), INDEX IDX_E414CBF8217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE badge_holder ADD CONSTRAINT FK_E414CBF8F7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id)');
        $this->addSql('ALTER TABLE badge_holder ADD CONSTRAINT FK_E414CBF8217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE badge_holder DROP FOREIGN KEY FK_E414CBF8F7A2C2FC');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE badge_holder');
    }
}
