<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240317080630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD long_description TEXT NOT NULL, ADD github_link VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(500) NOT NULL');
        $this->addSql('ALTER TABLE skill ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE skill ADD CONSTRAINT FK_5E3DE477166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_5E3DE477166D1F9C ON skill (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP long_description, DROP github_link, CHANGE description description VARCHAR(1000) NOT NULL');
        $this->addSql('ALTER TABLE skill DROP FOREIGN KEY FK_5E3DE477166D1F9C');
        $this->addSql('DROP INDEX IDX_5E3DE477166D1F9C ON skill');
        $this->addSql('ALTER TABLE skill DROP project_id');
    }
}
