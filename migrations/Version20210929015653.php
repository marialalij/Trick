<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210929015653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick RENAME INDEX idx_d8f0a91ef675f31b TO IDX_1931861AF675F31B');
        $this->addSql('ALTER TABLE trick RENAME INDEX idx_d8f0a91e12469de2 TO IDX_1931861A12469DE2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Trick RENAME INDEX idx_1931861a12469de2 TO IDX_D8F0A91E12469DE2');
        $this->addSql('ALTER TABLE Trick RENAME INDEX idx_1931861af675f31b TO IDX_D8F0A91EF675F31B');
    }
}
