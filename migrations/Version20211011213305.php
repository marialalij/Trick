<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211011213305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FB281BE2E');
        $this->addSql('DROP INDEX IDX_C53D045FB281BE2E ON image');
        $this->addSql('ALTER TABLE image CHANGE trick_id trick INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FD8F0A91E FOREIGN KEY (trick) REFERENCES Trick (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_C53D045FD8F0A91E ON image (trick)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FD8F0A91E');
        $this->addSql('DROP INDEX IDX_C53D045FD8F0A91E ON image');
        $this->addSql('ALTER TABLE image CHANGE trick trick_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C53D045FB281BE2E ON image (trick_id)');
        $this->addSql('ALTER TABLE Trick CHANGE author_id author_id INT NOT NULL');
    }
}
