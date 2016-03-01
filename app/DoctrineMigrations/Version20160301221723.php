<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160301221723 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competition_entry DROP FOREIGN KEY FK_D896AFA6613FECDF');
        $this->addSql('ALTER TABLE competition_session_round DROP FOREIGN KEY FK_E9469042613FECDF');
        $this->addSql('DROP TABLE competition_entry');
        $this->addSql('DROP TABLE competition_session');
        $this->addSql('DROP TABLE competition_session_round');
        $this->addSql('ALTER TABLE competition ADD date DATE NOT NULL, DROP description, DROP hosted, DROP location, DROP entryOpen, DROP entryClose, CHANGE name name VARCHAR(255) NOT NULL');

        // migrate record_holder
        $this->addSql('ALTER TABLE record_holder ADD competition_id INT DEFAULT NULL');
        $this->addSql('INSERT INTO competition (name, date) SELECT DISTINCT location, date FROM record_holder');
        $this->addSql('UPDATE record_holder JOIN competition ON record_holder.location = competition.name AND record_holder.date = competition.date SET competition_id = competition.id');
        $this->addSql('ALTER TABLE record_holder DROP location');
        $this->addSql('ALTER TABLE record_holder ADD CONSTRAINT FK_B4F42EC37B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('CREATE INDEX IDX_B4F42EC37B39D312 ON record_holder (competition_id)');

        // migrate score
        $this->addSql('ALTER TABLE score ADD competition_id INT DEFAULT NULL');
        $this->addSql('INSERT INTO competition (name, date) VALUES ("Migrated Competition", "1990-01-01")');
        $this->addSql('UPDATE score JOIN competition ON competition.name = "Migrated Competition" SET competition_id = competition.id WHERE score.competition = TRUE');
        $this->addSql('ALTER TABLE score DROP competition');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_329937517B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('CREATE INDEX IDX_329937517B39D312 ON score (competition_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competition_entry (id INT AUTO_INCREMENT NOT NULL, score_id INT DEFAULT NULL, person_id INT DEFAULT NULL, club_id INT DEFAULT NULL, session_id INT DEFAULT NULL, round_id INT DEFAULT NULL, date_approved DATETIME DEFAULT NULL, boss_number INT DEFAULT NULL, target_number INT DEFAULT NULL, bowtype VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, skill VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, gender VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, registered DATETIME DEFAULT NULL, date_entered DATETIME NOT NULL, UNIQUE INDEX UNIQ_D896AFA612EB0A51 (score_id), INDEX IDX_D896AFA661190A32 (club_id), INDEX IDX_D896AFA6217BBB47 (person_id), INDEX IDX_D896AFA6A6005CA0 (round_id), INDEX IDX_D896AFA6613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_session (id INT AUTO_INCREMENT NOT NULL, competition_id INT DEFAULT NULL, startTime DATETIME NOT NULL, bossCount INT NOT NULL, targetCount INT NOT NULL, detailCount INT NOT NULL, INDEX IDX_CFF1157A7B39D312 (competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_session_round (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, round_id INT DEFAULT NULL, INDEX IDX_E9469042613FECDF (session_id), INDEX IDX_E9469042A6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA612EB0A51 FOREIGN KEY (score_id) REFERENCES score (id)');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA661190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA6613FECDF FOREIGN KEY (session_id) REFERENCES competition_session (id)');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA6A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE competition_session ADD CONSTRAINT FK_CFF1157A7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE competition_session_round ADD CONSTRAINT FK_E9469042613FECDF FOREIGN KEY (session_id) REFERENCES competition_session (id)');
        $this->addSql('ALTER TABLE competition_session_round ADD CONSTRAINT FK_E9469042A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE competition ADD description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD hosted TINYINT(1) NOT NULL, ADD location VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD entryOpen DATETIME DEFAULT NULL, ADD entryClose DATETIME DEFAULT NULL, DROP date, CHANGE name name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE record_holder DROP FOREIGN KEY FK_B4F42EC37B39D312');
        $this->addSql('DROP INDEX IDX_B4F42EC37B39D312 ON record_holder');
        $this->addSql('ALTER TABLE record_holder ADD location VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP competition_id');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_329937517B39D312');
        $this->addSql('DROP INDEX IDX_329937517B39D312 ON score');
        $this->addSql('ALTER TABLE score ADD competition TINYINT(1) NOT NULL, DROP competition_id');
    }
}
