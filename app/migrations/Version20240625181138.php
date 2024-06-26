<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240625181138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP alias');
        $this->addSql('ALTER TABLE photos CHANGE description description TINYTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD name VARCHAR(180) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments ADD alias VARCHAR(64) NOT NULL');
        $this->addSql('ALTER TABLE photos CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP name');
    }
}
