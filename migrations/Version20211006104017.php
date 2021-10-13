<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211006104017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP slug');
        $this->addSql('ALTER TABLE trick RENAME INDEX idx_d8f0a91ef675f31b TO IDX_1931861AF675F31B');
        $this->addSql('ALTER TABLE trick RENAME INDEX idx_d8f0a91e12469de2 TO IDX_1931861A12469DE2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Trick ADD slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE Trick RENAME INDEX idx_1931861a12469de2 TO IDX_D8F0A91E12469DE2');
        $this->addSql('ALTER TABLE Trick RENAME INDEX idx_1931861af675f31b TO IDX_D8F0A91EF675F31B');
    }
}
