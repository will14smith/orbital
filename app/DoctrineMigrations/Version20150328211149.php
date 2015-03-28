<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150328211149 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, info_only TINYINT(1) NOT NULL, location VARCHAR(255) DEFAULT NULL, date DATETIME NOT NULL, boss_count INT DEFAULT NULL, target_count INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_round (competition_id INT NOT NULL, round_id INT NOT NULL, INDEX IDX_3659D8E27B39D312 (competition_id), INDEX IDX_3659D8E2A6005CA0 (round_id), PRIMARY KEY(competition_id, round_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_entry (id INT AUTO_INCREMENT NOT NULL, competition_id INT DEFAULT NULL, club_id INT DEFAULT NULL, person_id INT DEFAULT NULL, score_id INT DEFAULT NULL, date_approved DATETIME DEFAULT NULL, boss_number INT DEFAULT NULL, target_number INT DEFAULT NULL, INDEX IDX_D896AFA67B39D312 (competition_id), INDEX IDX_D896AFA661190A32 (club_id), INDEX IDX_D896AFA6217BBB47 (person_id), UNIQUE INDEX UNIQ_D896AFA612EB0A51 (score_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competition_round ADD CONSTRAINT FK_3659D8E27B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE competition_round ADD CONSTRAINT FK_3659D8E2A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA67B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA661190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE competition_entry ADD CONSTRAINT FK_D896AFA612EB0A51 FOREIGN KEY (score_id) REFERENCES score (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competition_round DROP FOREIGN KEY FK_3659D8E27B39D312');
        $this->addSql('ALTER TABLE competition_entry DROP FOREIGN KEY FK_D896AFA67B39D312');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE competition_round');
        $this->addSql('DROP TABLE competition_entry');
    }
}
