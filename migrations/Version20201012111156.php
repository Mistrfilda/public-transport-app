<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201012111156 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prague_transport_restriction (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', transport_restriction_id VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, publish_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', valid_from DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', valid_to DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', priority INT NOT NULL, affected_lines LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_F51A68D83C00FC52 (transport_restriction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE prague_transport_restriction');
    }
}
