<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240326205739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf($this->connection->createSchemaManager()->tablesExist(['Users']), 'Users table already exists');
        $this->addSql('CREATE TABLE Users (user_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB NOT NULL, passwordhash CLOB NOT NULL, email CLOB NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D5428AEDF85E0677 ON Users (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Users');
    }
}
