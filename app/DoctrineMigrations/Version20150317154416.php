<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150317154416 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, name_preferred VARCHAR(200) DEFAULT NULL, agb_number VARCHAR(50) DEFAULT NULL, cid VARCHAR(50) DEFAULT NULL, cuser VARCHAR(50) DEFAULT NULL, email VARCHAR(400) DEFAULT NULL, password VARCHAR(256) DEFAULT NULL, mobile VARCHAR(50) DEFAULT NULL, gender VARCHAR(50) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, skill VARCHAR(50) NOT NULL, bowtype VARCHAR(50) DEFAULT NULL, club_bow VARCHAR(50) DEFAULT NULL, admin TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE person');
    }
}
