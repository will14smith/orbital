<?php

namespace Application\Migrations;

use AppBundle\Services\Enum\Skill;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160506120733 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person ADD date_started DATE NOT NULL');

        // previous 1st Sept
        $noviceDate = Skill::normaliseStartDate(new \DateTime('now'));
        $seniorDate = clone $noviceDate;
        $seniorDate->sub(new \DateInterval('P1Y'));

        $this->addSql('UPDATE person SET date_started = CASE skill WHEN ? THEN ? ELSE ? END',
            [Skill::SENIOR, $seniorDate, $noviceDate],
            [Type::STRING, Type::DATE, Type::DATE]
        );

        $this->addSql('ALTER TABLE person DROP skill');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person ADD skill VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci');
        // TODO
        $this->addSql('ALTER TABLE person DROP date_started');
    }
}
