<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220731161128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_entity (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, entity_id INT DEFAULT NULL, roles JSON DEFAULT NULL, INDEX IDX_6B7A5F55A76ED395 (user_id), INDEX IDX_6B7A5F5581257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_entity ADD CONSTRAINT FK_6B7A5F55A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_entity ADD CONSTRAINT FK_6B7A5F5581257D5D FOREIGN KEY (entity_id) REFERENCES entity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_entity');
    }
}
