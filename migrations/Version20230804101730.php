<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230804101730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE academic_level (id UUID NOT NULL, name_fr VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, academic_year VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN academic_level.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE building (id UUID NOT NULL, campus_id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E16F61D4AF5D55E1 ON building (campus_id)');
        $this->addSql('COMMENT ON COLUMN building.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN building.campus_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE campus (id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN campus.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "student" (id UUID NOT NULL, tutoring_id UUID DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles TEXT NOT NULL, enabled BOOLEAN DEFAULT false NOT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF33E7927C74 ON "student" (email)');
        $this->addSql('CREATE INDEX IDX_B723AF33DC677BAB ON "student" (tutoring_id)');
        $this->addSql('COMMENT ON COLUMN "student".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "student".tutoring_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "student".roles IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE tutoring (id UUID NOT NULL, building_id UUID NOT NULL, academic_level_id UUID NOT NULL, name VARCHAR(255) NOT NULL, room VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D6213AAE4D2A7E12 ON tutoring (building_id)');
        $this->addSql('CREATE INDEX IDX_D6213AAE6081C3B0 ON tutoring (academic_level_id)');
        $this->addSql('COMMENT ON COLUMN tutoring.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring.building_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring.academic_level_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tutoring_session (id UUID NOT NULL, created_by_id UUID NOT NULL, building_id UUID DEFAULT NULL, tutoring_id UUID NOT NULL, start_date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_remote BOOLEAN NOT NULL, room VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DD9D6761B03A8386 ON tutoring_session (created_by_id)');
        $this->addSql('CREATE INDEX IDX_DD9D67614D2A7E12 ON tutoring_session (building_id)');
        $this->addSql('CREATE INDEX IDX_DD9D6761DC677BAB ON tutoring_session (tutoring_id)');
        $this->addSql('COMMENT ON COLUMN tutoring_session.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring_session.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring_session.building_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring_session.tutoring_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tutoring_session_tutor (tutoring_session_id UUID NOT NULL, student_id UUID NOT NULL, PRIMARY KEY(tutoring_session_id, student_id))');
        $this->addSql('CREATE INDEX IDX_31FE426C8B567663 ON tutoring_session_tutor (tutoring_session_id)');
        $this->addSql('CREATE INDEX IDX_31FE426CCB944F1A ON tutoring_session_tutor (student_id)');
        $this->addSql('COMMENT ON COLUMN tutoring_session_tutor.tutoring_session_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring_session_tutor.student_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tutoring_session_tutored (tutoring_session_id UUID NOT NULL, student_id UUID NOT NULL, PRIMARY KEY(tutoring_session_id, student_id))');
        $this->addSql('CREATE INDEX IDX_42161DB58B567663 ON tutoring_session_tutored (tutoring_session_id)');
        $this->addSql('CREATE INDEX IDX_42161DB5CB944F1A ON tutoring_session_tutored (student_id)');
        $this->addSql('COMMENT ON COLUMN tutoring_session_tutored.tutoring_session_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring_session_tutored.student_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "student" ADD CONSTRAINT FK_B723AF33DC677BAB FOREIGN KEY (tutoring_id) REFERENCES tutoring (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring ADD CONSTRAINT FK_D6213AAE4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring ADD CONSTRAINT FK_D6213AAE6081C3B0 FOREIGN KEY (academic_level_id) REFERENCES academic_level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session ADD CONSTRAINT FK_DD9D6761B03A8386 FOREIGN KEY (created_by_id) REFERENCES "student" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session ADD CONSTRAINT FK_DD9D67614D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session ADD CONSTRAINT FK_DD9D6761DC677BAB FOREIGN KEY (tutoring_id) REFERENCES tutoring (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session_tutor ADD CONSTRAINT FK_31FE426C8B567663 FOREIGN KEY (tutoring_session_id) REFERENCES tutoring_session (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session_tutor ADD CONSTRAINT FK_31FE426CCB944F1A FOREIGN KEY (student_id) REFERENCES "student" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session_tutored ADD CONSTRAINT FK_42161DB58B567663 FOREIGN KEY (tutoring_session_id) REFERENCES tutoring_session (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session_tutored ADD CONSTRAINT FK_42161DB5CB944F1A FOREIGN KEY (student_id) REFERENCES "student" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE building DROP CONSTRAINT FK_E16F61D4AF5D55E1');
        $this->addSql('ALTER TABLE "student" DROP CONSTRAINT FK_B723AF33DC677BAB');
        $this->addSql('ALTER TABLE tutoring DROP CONSTRAINT FK_D6213AAE4D2A7E12');
        $this->addSql('ALTER TABLE tutoring DROP CONSTRAINT FK_D6213AAE6081C3B0');
        $this->addSql('ALTER TABLE tutoring_session DROP CONSTRAINT FK_DD9D6761B03A8386');
        $this->addSql('ALTER TABLE tutoring_session DROP CONSTRAINT FK_DD9D67614D2A7E12');
        $this->addSql('ALTER TABLE tutoring_session DROP CONSTRAINT FK_DD9D6761DC677BAB');
        $this->addSql('ALTER TABLE tutoring_session_tutor DROP CONSTRAINT FK_31FE426C8B567663');
        $this->addSql('ALTER TABLE tutoring_session_tutor DROP CONSTRAINT FK_31FE426CCB944F1A');
        $this->addSql('ALTER TABLE tutoring_session_tutored DROP CONSTRAINT FK_42161DB58B567663');
        $this->addSql('ALTER TABLE tutoring_session_tutored DROP CONSTRAINT FK_42161DB5CB944F1A');
        $this->addSql('DROP TABLE academic_level');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE campus');
        $this->addSql('DROP TABLE "student"');
        $this->addSql('DROP TABLE tutoring');
        $this->addSql('DROP TABLE tutoring_session');
        $this->addSql('DROP TABLE tutoring_session_tutor');
        $this->addSql('DROP TABLE tutoring_session_tutored');
    }
}
