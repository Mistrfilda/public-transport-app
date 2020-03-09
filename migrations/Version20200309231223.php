<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200309231223 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE trip_statistic_data CHANGE date date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE oldest_known_position oldest_known_position DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE newest_known_position newest_known_position DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE trip_statistic_data CHANGE date date DATETIME NOT NULL, CHANGE oldest_known_position oldest_known_position DATETIME NOT NULL, CHANGE newest_known_position newest_known_position DATETIME NOT NULL');
    }
}
