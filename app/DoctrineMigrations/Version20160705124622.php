<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160705124622 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE badgesheet_badgeholder DROP FOREIGN KEY FK_18F05CCAF9DEEE64');
        $this->addSql('DROP TABLE badge_sheet');
        $this->addSql('DROP TABLE badgesheet_badgeholder');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE badge_sheet (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE badgesheet_badgeholder (badgesheet_id INT NOT NULL, badgeholder_id INT NOT NULL, INDEX IDX_18F05CCAF9DEEE64 (badgesheet_id), INDEX IDX_18F05CCA32FF5C1E (badgeholder_id), PRIMARY KEY(badgesheet_id, badgeholder_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE badgesheet_badgeholder ADD CONSTRAINT FK_18F05CCA32FF5C1E FOREIGN KEY (badgeholder_id) REFERENCES badge_holder (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE badgesheet_badgeholder ADD CONSTRAINT FK_18F05CCAF9DEEE64 FOREIGN KEY (badgesheet_id) REFERENCES badge_sheet (id) ON DELETE CASCADE');
    }
}
