<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240326210057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user roles property';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Users ADD COLUMN roles CLOB NOT NULL DEFAULT "[]"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__Users AS SELECT user_id, username, passwordhash, email FROM Users');
        $this->addSql('DROP TABLE Users');
        $this->addSql('CREATE TABLE Users (user_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB NOT NULL, passwordhash CLOB NOT NULL, email CLOB NOT NULL)');
        $this->addSql('INSERT INTO Users (user_id, username, passwordhash, email) SELECT user_id, username, passwordhash, email FROM __temp__Users');
        $this->addSql('DROP TABLE __temp__Users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D5428AEDF85E0677 ON Users (username)');
    }
}
