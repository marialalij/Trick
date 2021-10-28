<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211019122227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_1931861AF675F31B');
        $this->addSql('ALTER TABLE trick CHANGE author_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_1931861AF675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Trick DROP FOREIGN KEY FK_1931861AF675F31B');
        $this->addSql('ALTER TABLE Trick CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Trick ADD CONSTRAINT FK_1931861AF675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
