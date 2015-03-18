<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150318134743 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE round_target (id INT AUTO_INCREMENT NOT NULL, round_id INT DEFAULT NULL, distance_value NUMERIC(10, 0) NOT NULL, distance_unit VARCHAR(20) NOT NULL, target_value NUMERIC(10, 0) NOT NULL, target_unit VARCHAR(20) NOT NULL, arrow_count INT NOT NULL, end_size INT NOT NULL, INDEX IDX_82D81650A6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE round_target ADD CONSTRAINT FK_82D81650A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE round_target DROP FOREIGN KEY FK_82D81650A6005CA0');
        $this->addSql('DROP TABLE round_target');
        $this->addSql('DROP TABLE round');
    }
}
