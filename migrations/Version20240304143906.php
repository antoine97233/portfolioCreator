<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304143906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE score_skill (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, skill_id INT NOT NULL, score INT NOT NULL, INDEX IDX_E77128FFA76ED395 (user_id), INDEX IDX_E77128FF5585C142 (skill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE score_skill ADD CONSTRAINT FK_E77128FFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE score_skill ADD CONSTRAINT FK_E77128FF5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id)');
        $this->addSql('ALTER TABLE skill DROP score');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE score_skill DROP FOREIGN KEY FK_E77128FFA76ED395');
        $this->addSql('ALTER TABLE score_skill DROP FOREIGN KEY FK_E77128FF5585C142');
        $this->addSql('DROP TABLE score_skill');
        $this->addSql('ALTER TABLE skill ADD score INT DEFAULT NULL');
    }
}
