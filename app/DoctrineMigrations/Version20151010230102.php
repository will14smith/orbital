<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151010230102 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE badge CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE competition_entry CHANGE bowtype bowtype VARCHAR(255) NOT NULL, CHANGE skill skill VARCHAR(255) NOT NULL, CHANGE gender gender VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE score CHANGE skill skill VARCHAR(50) NOT NULL, CHANGE bowtype bowtype VARCHAR(50) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE badge CHANGE description description VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE competition_entry CHANGE bowtype bowtype VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE skill skill VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE gender gender VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE score CHANGE skill skill VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE bowtype bowtype VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
