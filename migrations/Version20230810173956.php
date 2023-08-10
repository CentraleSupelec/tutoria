<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230810173956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tutoring_student (tutoring_id UUID NOT NULL, student_id UUID NOT NULL, PRIMARY KEY(tutoring_id, student_id))');
        $this->addSql('CREATE INDEX IDX_BAFA1D86DC677BAB ON tutoring_student (tutoring_id)');
        $this->addSql('CREATE INDEX IDX_BAFA1D86CB944F1A ON tutoring_student (student_id)');
        $this->addSql('COMMENT ON COLUMN tutoring_student.tutoring_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring_student.student_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tutoring_student ADD CONSTRAINT FK_BAFA1D86DC677BAB FOREIGN KEY (tutoring_id) REFERENCES tutoring (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_student ADD CONSTRAINT FK_BAFA1D86CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring ALTER room DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tutoring_student DROP CONSTRAINT FK_BAFA1D86DC677BAB');
        $this->addSql('ALTER TABLE tutoring_student DROP CONSTRAINT FK_BAFA1D86CB944F1A');
        $this->addSql('DROP TABLE tutoring_student');
        $this->addSql('ALTER TABLE tutoring ALTER room SET NOT NULL');
    }
}
