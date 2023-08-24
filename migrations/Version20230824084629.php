<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824084629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tutoring ADD default_start_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE tutoring ADD default_end_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE tutoring DROP start_time');
        $this->addSql('ALTER TABLE tutoring DROP end_time');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tutoring ADD start_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE tutoring ADD end_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE tutoring DROP default_start_time');
        $this->addSql('ALTER TABLE tutoring DROP default_end_time');
    }
}
