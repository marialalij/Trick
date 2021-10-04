<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211004122129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE image_trick');
        $this->addSql('DROP TABLE trick_image');
        $this->addSql('DROP TABLE trick_video');
        $this->addSql('DROP TABLE video_trick');
        $this->addSql('ALTER TABLE image ADD tricks_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F3B153154 FOREIGN KEY (tricks_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_C53D045F3B153154 ON image (tricks_id)');
        $this->addSql('ALTER TABLE video ADD tricks_id INT DEFAULT NULL, ADD url VARCHAR(255) NOT NULL, DROP name');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C3B153154 FOREIGN KEY (tricks_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C3B153154 ON video (tricks_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_trick (image_id INT NOT NULL, trick_id INT NOT NULL, INDEX IDX_CFB52DB73DA5256D (image_id), INDEX IDX_CFB52DB7B281BE2E (trick_id), PRIMARY KEY(image_id, trick_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE trick_image (trick_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_E1204E03DA5256D (image_id), INDEX IDX_E1204E0B281BE2E (trick_id), PRIMARY KEY(trick_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE trick_video (trick_id INT NOT NULL, video_id INT NOT NULL, INDEX IDX_B7E8DA9329C1004E (video_id), INDEX IDX_B7E8DA93B281BE2E (trick_id), PRIMARY KEY(trick_id, video_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE video_trick (video_id INT NOT NULL, trick_id INT NOT NULL, INDEX IDX_5792A0BC29C1004E (video_id), INDEX IDX_5792A0BCB281BE2E (trick_id), PRIMARY KEY(video_id, trick_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE image_trick ADD CONSTRAINT FK_CFB52DB73DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_trick ADD CONSTRAINT FK_CFB52DB7B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_image ADD CONSTRAINT FK_E1204E03DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_image ADD CONSTRAINT FK_E1204E0B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_video ADD CONSTRAINT FK_B7E8DA9329C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_video ADD CONSTRAINT FK_B7E8DA93B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video_trick ADD CONSTRAINT FK_5792A0BC29C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video_trick ADD CONSTRAINT FK_5792A0BCB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F3B153154');
        $this->addSql('DROP INDEX IDX_C53D045F3B153154 ON image');
        $this->addSql('ALTER TABLE image DROP tricks_id');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C3B153154');
        $this->addSql('DROP INDEX IDX_7CC7DA2C3B153154 ON video');
        $this->addSql('ALTER TABLE video ADD name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP tricks_id, DROP url');
    }
}
