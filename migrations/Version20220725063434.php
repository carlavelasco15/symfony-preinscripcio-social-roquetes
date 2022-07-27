<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220725063434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD email_ent_new_prescription TINYINT(1) DEFAULT NULL, ADD email_ent_waiting_list TINYINT(1) DEFAULT NULL, ADD email_ent_finished_activity TINYINT(1) DEFAULT NULL, ADD email_ent_attendance_form TINYINT(1) DEFAULT NULL, ADD email_ent_participant_rating TINYINT(1) DEFAULT NULL, ADD email_ser_new_activity TINYINT(1) DEFAULT NULL, ADD email_ser_attendance_form TINYINT(1) DEFAULT NULL, ADD email_ser_deleted_activity TINYINT(1) DEFAULT NULL, ADD email_ser_from_waiting_to_open TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP email_ent_new_prescription, DROP email_ent_waiting_list, DROP email_ent_finished_activity, DROP email_ent_attendance_form, DROP email_ent_participant_rating, DROP email_ser_new_activity, DROP email_ser_attendance_form, DROP email_ser_deleted_activity, DROP email_ser_from_waiting_to_open');
    }
}
