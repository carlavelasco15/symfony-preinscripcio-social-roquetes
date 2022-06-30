<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220624144716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity CHANGE price price VARCHAR(50) DEFAULT NULL, CHANGE session_count session_count INT DEFAULT NULL, CHANGE places places INT DEFAULT NULL, CHANGE schedule schedule VARCHAR(255) DEFAULT NULL, CHANGE start_date start_date DATE DEFAULT NULL, CHANGE end_date end_date DATE DEFAULT NULL, CHANGE is_visible is_visible TINYINT(1) DEFAULT 1, CHANGE is_deleted is_deleted TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE ticket CHANGE activity_id activity_id INT DEFAULT NULL, CHANGE participant_id participant_id INT DEFAULT NULL, CHANGE ticket_status_id ticket_status_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity CHANGE price price VARCHAR(50) NOT NULL, CHANGE session_count session_count INT NOT NULL, CHANGE places places INT NOT NULL, CHANGE schedule schedule VARCHAR(255) NOT NULL, CHANGE start_date start_date DATE NOT NULL, CHANGE end_date end_date DATE NOT NULL, CHANGE is_visible is_visible TINYINT(1) DEFAULT 1 NOT NULL, CHANGE is_deleted is_deleted TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE ticket CHANGE activity_id activity_id INT NOT NULL, CHANGE participant_id participant_id INT NOT NULL, CHANGE ticket_status_id ticket_status_id INT NOT NULL');
    }
}
