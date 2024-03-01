<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301123437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD fullname VARCHAR(255) NOT NULL, ADD slug VARCHAR(255) NOT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD subtitle VARCHAR(255) DEFAULT NULL, ADD short_description LONGTEXT DEFAULT NULL, ADD long_description LONGTEXT DEFAULT NULL, ADD is_open_to_work TINYINT(1) DEFAULT NULL, ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD thumbnail VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user DROP fullname, DROP slug, DROP title, DROP subtitle, DROP short_description, DROP long_description, DROP is_open_to_work, DROP updated_at, DROP thumbnail, CHANGE email email VARCHAR(255) NOT NULL');
    }
}
