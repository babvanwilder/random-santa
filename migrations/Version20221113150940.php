<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221113150940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE santa (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, closer_id INT DEFAULT NULL, year INT NOT NULL, close TINYINT(1) NOT NULL, date_close DATETIME NOT NULL, INDEX IDX_C865F6727E3C61F9 (owner_id), INDEX IDX_C865F672FD0FD350 (closer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE santa ADD CONSTRAINT FK_C865F6727E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE santa ADD CONSTRAINT FK_C865F672FD0FD350 FOREIGN KEY (closer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD lastname VARCHAR(255) NOT NULL, ADD firstname VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE santa DROP FOREIGN KEY FK_C865F6727E3C61F9');
        $this->addSql('ALTER TABLE santa DROP FOREIGN KEY FK_C865F672FD0FD350');
        $this->addSql('DROP TABLE santa');
        $this->addSql('ALTER TABLE user DROP lastname, DROP firstname');
    }
}
