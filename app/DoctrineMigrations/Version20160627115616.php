<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160627115616 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person 
  ADD username VARCHAR(255) NOT NULL, 
  ADD username_canonical VARCHAR(255) NOT NULL, 
  ADD email_canonical VARCHAR(255) NOT NULL, 
  ADD last_login DATETIME DEFAULT NULL, 
  ADD locked TINYINT(1) NOT NULL, 
  ADD expired TINYINT(1) NOT NULL, 
  ADD expires_at DATETIME DEFAULT NULL, 
  ADD confirmation_token VARCHAR(255) DEFAULT NULL, 
  ADD password_requested_at DATETIME DEFAULT NULL, 
  ADD roles LONGTEXT NOT NULL comment \'(DC2Type:array)\', 
  ADD credentials_expired TINYINT(1) NOT NULL, 
  ADD credentials_expire_at DATETIME DEFAULT NULL, 
  ADD enabled TINYINT(1) NOT NULL
');

        $this->addSql('UPDATE person SET cuser = id WHERE cuser IS NULL');
        $this->addSql('UPDATE person SET email = id WHERE email IS NULL OR email = \'\'');

        $this->addSql('UPDATE person SET roles = \'a:0:{}\' WHERE admin = false');
        $this->addSql('UPDATE person SET roles = \'a:1:{i:0;s:10:"ROLE_ADMIN";}\', enabled = 1 WHERE admin = true');

        $this->addSql('UPDATE person SET username = cuser, username_canonical = LOWER(cuser), password = \'\', salt = \'\', email = LEFT(email, 255), email_canonical = LOWER(LEFT(email, 255))');
        $this->addSql('ALTER TABLE person DROP cuser, DROP admin, CHANGE email email VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE salt salt VARCHAR(255) NOT NULL');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD17692FC23A8 ON person (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD176A0D96FBF ON person (email_canonical)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_34DCD17692FC23A8 ON person');
        $this->addSql('DROP INDEX UNIQ_34DCD176A0D96FBF ON person');
        $this->addSql('ALTER TABLE person ADD cuser VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, ADD admin TINYINT(1) NOT NULL, DROP username, DROP username_canonical, DROP email_canonical, DROP enabled, DROP last_login, DROP locked, DROP expired, DROP expires_at, DROP confirmation_token, DROP password_requested_at, DROP roles, DROP credentials_expired, DROP credentials_expire_at, CHANGE email email VARCHAR(400) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE salt salt VARCHAR(256) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE password password VARCHAR(256) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
