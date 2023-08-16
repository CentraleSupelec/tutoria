<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824133356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student DROP CONSTRAINT fk_b723af33dc677bab');
        $this->addSql('DROP INDEX idx_b723af33dc677bab');
        $this->addSql('ALTER TABLE student DROP tutoring_id');
        $this->addSql('ALTER TABLE tutoring DROP CONSTRAINT fk_d6213aae4d2a7e12');
        $this->addSql('DROP INDEX idx_d6213aae4d2a7e12');
        $this->addSql('ALTER TABLE tutoring ADD default_week_days TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE tutoring ADD default_start_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE tutoring ADD default_end_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE tutoring RENAME COLUMN building_id TO default_building_id');
        $this->addSql('ALTER TABLE tutoring RENAME COLUMN room TO default_room');
        $this->addSql('COMMENT ON COLUMN tutoring.default_week_days IS \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE tutoring ADD CONSTRAINT FK_D6213AAE99D96DA4 FOREIGN KEY (default_building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D6213AAE99D96DA4 ON tutoring (default_building_id)');
        $this->addSql('ALTER TABLE tutoring_session ADD online_meeting_uri VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE student ADD tutoring_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN student.tutoring_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT fk_b723af33dc677bab FOREIGN KEY (tutoring_id) REFERENCES tutoring (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b723af33dc677bab ON student (tutoring_id)');
        $this->addSql('ALTER TABLE tutoring_session DROP online_meeting_uri');
        $this->addSql('ALTER TABLE tutoring DROP CONSTRAINT FK_D6213AAE99D96DA4');
        $this->addSql('DROP INDEX IDX_D6213AAE99D96DA4');
        $this->addSql('ALTER TABLE tutoring DROP default_week_days');
        $this->addSql('ALTER TABLE tutoring DROP default_start_time');
        $this->addSql('ALTER TABLE tutoring DROP default_end_time');
        $this->addSql('ALTER TABLE tutoring RENAME COLUMN default_building_id TO building_id');
        $this->addSql('ALTER TABLE tutoring RENAME COLUMN default_room TO room');
        $this->addSql('ALTER TABLE tutoring ADD CONSTRAINT fk_d6213aae4d2a7e12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d6213aae4d2a7e12 ON tutoring (building_id)');
    }
}
