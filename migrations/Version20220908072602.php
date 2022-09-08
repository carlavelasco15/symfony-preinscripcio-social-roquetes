<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220908072602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant ADD entity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1181257D5D FOREIGN KEY (entity_id) REFERENCES entity (id)');
        $this->addSql('CREATE INDEX IDX_D79F6B1181257D5D ON participant (entity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B1181257D5D');
        $this->addSql('DROP INDEX IDX_D79F6B1181257D5D ON participant');
        $this->addSql('ALTER TABLE participant DROP entity_id');
    }
}
