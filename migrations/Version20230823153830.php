<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230823153830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tutoring_session_tutee (tutoring_session_id UUID NOT NULL, student_id UUID NOT NULL, PRIMARY KEY(tutoring_session_id, student_id))');
        $this->addSql('CREATE INDEX IDX_48C22F218B567663 ON tutoring_session_tutee (tutoring_session_id)');
        $this->addSql('CREATE INDEX IDX_48C22F21CB944F1A ON tutoring_session_tutee (student_id)');
        $this->addSql('COMMENT ON COLUMN tutoring_session_tutee.tutoring_session_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring_session_tutee.student_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tutoring_session_tutee ADD CONSTRAINT FK_48C22F218B567663 FOREIGN KEY (tutoring_session_id) REFERENCES tutoring_session (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session_tutee ADD CONSTRAINT FK_48C22F21CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session_tutored DROP CONSTRAINT fk_42161db58b567663');
        $this->addSql('ALTER TABLE tutoring_session_tutored DROP CONSTRAINT fk_42161db5cb944f1a');
        $this->addSql('DROP TABLE tutoring_session_tutored');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE tutoring_session_tutored (tutoring_session_id UUID NOT NULL, student_id UUID NOT NULL, PRIMARY KEY(tutoring_session_id, student_id))');
        $this->addSql('CREATE INDEX idx_42161db5cb944f1a ON tutoring_session_tutored (student_id)');
        $this->addSql('CREATE INDEX idx_42161db58b567663 ON tutoring_session_tutored (tutoring_session_id)');
        $this->addSql('COMMENT ON COLUMN tutoring_session_tutored.tutoring_session_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tutoring_session_tutored.student_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tutoring_session_tutored ADD CONSTRAINT fk_42161db58b567663 FOREIGN KEY (tutoring_session_id) REFERENCES tutoring_session (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session_tutored ADD CONSTRAINT fk_42161db5cb944f1a FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tutoring_session_tutee DROP CONSTRAINT FK_48C22F218B567663');
        $this->addSql('ALTER TABLE tutoring_session_tutee DROP CONSTRAINT FK_48C22F21CB944F1A');
        $this->addSql('DROP TABLE tutoring_session_tutee');
    }
}
