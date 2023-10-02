<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231002110406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE tutoring ADD academic_year VARCHAR(255) NOT NULL DEFAULT '2023-2024'");
        $this->addSql('UPDATE tutoring SET academic_year = academic_level.academic_year FROM academic_level WHERE tutoring.academic_level_id = academic_level.id;');
        $this->addSql('ALTER TABLE tutoring ALTER COLUMN academic_year DROP DEFAULT');
        $this->addSql('ALTER TABLE academic_level DROP academic_year');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE academic_level ADD academic_year VARCHAR(255) NOT NULL DEFAULT '2023-2024'");
        // Update academic year column based on a randomly selected tutoring that match academic level id of current record
        $this->addSql('UPDATE academic_level SET academic_year = (SELECT t.academic_year FROM tutoring t WHERE t.academic_level_id = academic_level.id LIMIT 1);');
        $this->addSql('ALTER TABLE academic_level ALTER COLUMN academic_year DROP DEFAULT');
        $this->addSql('ALTER TABLE tutoring DROP academic_year');
    }
}
