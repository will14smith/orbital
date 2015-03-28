<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150328144239 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE league (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, algo_name VARCHAR(255) DEFAULT NULL, open_date DATETIME NOT NULL, close_date DATETIME DEFAULT NULL, skill_limit VARCHAR(255) DEFAULT NULL, gender_limit VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE league_round (league_id INT NOT NULL, round_id INT NOT NULL, INDEX IDX_B203050258AFC4DE (league_id), INDEX IDX_B2030502A6005CA0 (round_id), PRIMARY KEY(league_id, round_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE league_match (id INT AUTO_INCREMENT NOT NULL, league_id INT DEFAULT NULL, challenger_id INT DEFAULT NULL, challengee_id INT DEFAULT NULL, round_id INT DEFAULT NULL, challenger_score_id INT DEFAULT NULL, challengee_score_id INT DEFAULT NULL, challenger_score_value INT DEFAULT NULL, challengee_score_value INT DEFAULT NULL, accepted TINYINT(1) NOT NULL, result TINYINT(1) DEFAULT NULL, date_challenged DATETIME NOT NULL, date_confirmed DATETIME DEFAULT NULL, INDEX IDX_DB62A3358AFC4DE (league_id), INDEX IDX_DB62A332D521FDF (challenger_id), INDEX IDX_DB62A33E09C70F9 (challengee_id), INDEX IDX_DB62A33A6005CA0 (round_id), INDEX IDX_DB62A33261F1BCE (challenger_score_id), INDEX IDX_DB62A33E36D815F (challengee_score_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE league_match_proof (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, match_id INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, INDEX IDX_489F6CF2217BBB47 (person_id), INDEX IDX_489F6CF22ABEACD6 (match_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE league_person (id INT AUTO_INCREMENT NOT NULL, league_id INT DEFAULT NULL, person_id INT DEFAULT NULL, date_added DATETIME NOT NULL, initial_position INT DEFAULT NULL, points INT NOT NULL, INDEX IDX_FB11A90058AFC4DE (league_id), INDEX IDX_FB11A900217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE league_round ADD CONSTRAINT FK_B203050258AFC4DE FOREIGN KEY (league_id) REFERENCES league (id)');
        $this->addSql('ALTER TABLE league_round ADD CONSTRAINT FK_B2030502A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE league_match ADD CONSTRAINT FK_DB62A3358AFC4DE FOREIGN KEY (league_id) REFERENCES league (id)');
        $this->addSql('ALTER TABLE league_match ADD CONSTRAINT FK_DB62A332D521FDF FOREIGN KEY (challenger_id) REFERENCES league_person (id)');
        $this->addSql('ALTER TABLE league_match ADD CONSTRAINT FK_DB62A33E09C70F9 FOREIGN KEY (challengee_id) REFERENCES league_person (id)');
        $this->addSql('ALTER TABLE league_match ADD CONSTRAINT FK_DB62A33A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE league_match ADD CONSTRAINT FK_DB62A33261F1BCE FOREIGN KEY (challenger_score_id) REFERENCES score (id)');
        $this->addSql('ALTER TABLE league_match ADD CONSTRAINT FK_DB62A33E36D815F FOREIGN KEY (challengee_score_id) REFERENCES score (id)');
        $this->addSql('ALTER TABLE league_match_proof ADD CONSTRAINT FK_489F6CF2217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE league_match_proof ADD CONSTRAINT FK_489F6CF22ABEACD6 FOREIGN KEY (match_id) REFERENCES league_match (id)');
        $this->addSql('ALTER TABLE league_person ADD CONSTRAINT FK_FB11A90058AFC4DE FOREIGN KEY (league_id) REFERENCES league (id)');
        $this->addSql('ALTER TABLE league_person ADD CONSTRAINT FK_FB11A900217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE league_round DROP FOREIGN KEY FK_B203050258AFC4DE');
        $this->addSql('ALTER TABLE league_match DROP FOREIGN KEY FK_DB62A3358AFC4DE');
        $this->addSql('ALTER TABLE league_person DROP FOREIGN KEY FK_FB11A90058AFC4DE');
        $this->addSql('ALTER TABLE league_match_proof DROP FOREIGN KEY FK_489F6CF22ABEACD6');
        $this->addSql('ALTER TABLE league_match DROP FOREIGN KEY FK_DB62A332D521FDF');
        $this->addSql('ALTER TABLE league_match DROP FOREIGN KEY FK_DB62A33E09C70F9');
        $this->addSql('DROP TABLE league');
        $this->addSql('DROP TABLE league_round');
        $this->addSql('DROP TABLE league_match');
        $this->addSql('DROP TABLE league_match_proof');
        $this->addSql('DROP TABLE league_person');
    }
}
