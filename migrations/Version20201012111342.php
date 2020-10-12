<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201012111342 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prague_transport_restriction CHANGE publish_date publish_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE valid_from valid_from DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE valid_to valid_to DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prague_transport_restriction CHANGE publish_date publish_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE valid_from valid_from DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE valid_to valid_to DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
