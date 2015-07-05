<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150621183435 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competition_session (id INT AUTO_INCREMENT NOT NULL, competition_id INT DEFAULT NULL, startTime DATETIME NOT NULL, bossCount INT NOT NULL, targetCount INT NOT NULL, detailCount INT NOT NULL, INDEX IDX_CFF1157A7B39D312 (competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_session_round (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, round_id INT DEFAULT NULL, INDEX IDX_E9469042613FECDF (session_id), INDEX IDX_E9469042A6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competition_session ADD CONSTRAINT FK_CFF1157A7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE competition_session_round ADD CONSTRAINT FK_E9469042613FECDF FOREIGN KEY (session_id) REFERENCES competition_session (id)');
        $this->addSql('ALTER TABLE competition_session_round ADD CONSTRAINT FK_E9469042A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('DROP TABLE competition_round');
        $this->addSql('ALTER TABLE competition DROP date, DROP boss_count, DROP target_count, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE info_only hosted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE competition_entry DROP FOREIGN KEY FK_D896AFA67B39D312');
        $this->addSql('DROP INDEX IDX_D896AFA67B39D312 ON competition_entry');
        // WARNING: this is SUPER BAD.
        $this->addSql('DELETE FROM competition_entry');
        $this->addSql('ALTER TABLE competition_entry ADD registered DATETIME DEFAULT NULL, CHANGE competition_id session_id INT DEFAULT');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA6613FECDF FOREIGN KEY (session_id) REFERENCES competition_session (id)');
        $this->addSql('CREATE INDEX IDX_D896AFA6613FECDF ON competition_entry (session_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competition_entry DROP FOREIGN KEY FK_D896AFA6613FECDF');
        $this->addSql('ALTER TABLE competition_session_round DROP FOREIGN KEY FK_E9469042613FECDF');
        $this->addSql('CREATE TABLE competition_round (competition_id INT NOT NULL, round_id INT NOT NULL, INDEX IDX_3659D8E27B39D312 (competition_id), INDEX IDX_3659D8E2A6005CA0 (round_id), PRIMARY KEY(competition_id, round_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competition_round ADD CONSTRAINT FK_3659D8E27B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE competition_round ADD CONSTRAINT FK_3659D8E2A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('DROP TABLE competition_session');
        $this->addSql('DROP TABLE competition_session_round');
        $this->addSql('ALTER TABLE competition ADD date DATETIME NOT NULL, ADD boss_count INT DEFAULT NULL, ADD target_count INT DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE hosted info_only TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX IDX_D896AFA6613FECDF ON competition_entry');
        $this->addSql('ALTER TABLE competition_entry DROP registered, CHANGE session_id competition_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA67B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('CREATE INDEX IDX_D896AFA67B39D312 ON competition_entry (competition_id)');
    }
}
