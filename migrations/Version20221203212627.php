<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221203212627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participate (id INT AUTO_INCREMENT NOT NULL, giver_id INT NOT NULL, receiver_id INT DEFAULT NULL, santa_id INT NOT NULL, INDEX IDX_D02B13875BD1D29 (giver_id), INDEX IDX_D02B138CD53EDB6 (receiver_id), INDEX IDX_D02B1384E0AAA2A (santa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participate ADD CONSTRAINT FK_D02B13875BD1D29 FOREIGN KEY (giver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participate ADD CONSTRAINT FK_D02B138CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participate ADD CONSTRAINT FK_D02B1384E0AAA2A FOREIGN KEY (santa_id) REFERENCES santa (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participate DROP FOREIGN KEY FK_D02B13875BD1D29');
        $this->addSql('ALTER TABLE participate DROP FOREIGN KEY FK_D02B138CD53EDB6');
        $this->addSql('ALTER TABLE participate DROP FOREIGN KEY FK_D02B1384E0AAA2A');
        $this->addSql('DROP TABLE participate');
    }
}
