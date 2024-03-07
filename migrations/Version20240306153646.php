<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306153646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C166D1F9C');
        $this->addSql('DROP INDEX IDX_6A2CA10C166D1F9C ON media');
        $this->addSql('ALTER TABLE media DROP project_id');
        $this->addSql('ALTER TABLE project ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EEEA9FDD75 ON project (media_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C166D1F9C ON media (project_id)');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEEA9FDD75');
        $this->addSql('DROP INDEX UNIQ_2FB3D0EEEA9FDD75 ON project');
        $this->addSql('ALTER TABLE project DROP media_id');
    }
}
