<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221203142214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE santa DROP FOREIGN KEY FK_C865F672FD0FD350');
        $this->addSql('DROP INDEX IDX_C865F672FD0FD350 ON santa');
        $this->addSql('ALTER TABLE santa ADD date_start DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD date_archived DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP year, DROP close, CHANGE date_close date_close DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE closer_id archiver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE santa ADD CONSTRAINT FK_C865F672A430C03C FOREIGN KEY (archiver_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C865F672A430C03C ON santa (archiver_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE santa DROP FOREIGN KEY FK_C865F672A430C03C');
        $this->addSql('DROP INDEX IDX_C865F672A430C03C ON santa');
        $this->addSql('ALTER TABLE santa ADD year INT NOT NULL, ADD close TINYINT(1) NOT NULL, DROP date_start, DROP date_archived, CHANGE date_close date_close DATETIME DEFAULT NULL, CHANGE archiver_id closer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE santa ADD CONSTRAINT FK_C865F672FD0FD350 FOREIGN KEY (closer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C865F672FD0FD350 ON santa (closer_id)');
    }
}
