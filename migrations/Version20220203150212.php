<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220203150212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cocktail ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cocktail ADD CONSTRAINT FK_7B4914D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7B4914D4A76ED395 ON cocktail (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cocktail DROP FOREIGN KEY FK_7B4914D4A76ED395');
        $this->addSql('DROP INDEX IDX_7B4914D4A76ED395 ON cocktail');
        $this->addSql('ALTER TABLE cocktail DROP user_id');
    }
}
