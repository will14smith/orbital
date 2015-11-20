<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151120175727 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE score_arrow');
        $this->addSql('ALTER TABLE competition_session DROP actualStartTime, DROP actualEndTime');
        $this->addSql('ALTER TABLE score DROP complete');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE score_arrow (id INT AUTO_INCREMENT NOT NULL, input_by_id INT DEFAULT NULL, score_id INT DEFAULT NULL, edit_by_id INT DEFAULT NULL, number INT NOT NULL, value VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, input_time DATETIME NOT NULL, edit_time DATETIME DEFAULT NULL, UNIQUE INDEX score_arrow_number_idx (score_id, number), INDEX IDX_617B22B612EB0A51 (score_id), INDEX IDX_617B22B61219E8B0 (input_by_id), INDEX IDX_617B22B693555579 (edit_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE score_arrow ADD CONSTRAINT FK_617B22B61219E8B0 FOREIGN KEY (input_by_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE score_arrow ADD CONSTRAINT FK_617B22B612EB0A51 FOREIGN KEY (score_id) REFERENCES score (id)');
        $this->addSql('ALTER TABLE score_arrow ADD CONSTRAINT FK_617B22B693555579 FOREIGN KEY (edit_by_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE competition_session ADD actualStartTime DATETIME DEFAULT NULL, ADD actualEndTime DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE score ADD complete TINYINT(1) NOT NULL');
    }
}
