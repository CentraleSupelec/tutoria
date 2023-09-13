<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915144208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE platform (id UUID NOT NULL, name VARCHAR(255) NOT NULL, audience VARCHAR(255) NOT NULL, oidc_authentication_url VARCHAR(255) DEFAULT NULL, o_auth2_access_token_url VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN platform.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE registration (id UUID NOT NULL, platform_id UUID NOT NULL, client_id VARCHAR(255) NOT NULL, deployment_id VARCHAR(255) NOT NULL, platform_jwks_url VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62A8A7A7FFE6496F ON registration (platform_id)');
        $this->addSql('COMMENT ON COLUMN registration.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN registration.platform_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7FFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE registration DROP CONSTRAINT FK_62A8A7A7FFE6496F');
        $this->addSql('DROP TABLE platform');
        $this->addSql('DROP TABLE registration');
    }
}
