<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200309195144 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE trip_statistic_data (id INT AUTO_INCREMENT NOT NULL, trip_id VARCHAR(255) NOT NULL, route_id VARCHAR(255) NOT NULL, final_station VARCHAR(255) NOT NULL, wheelchair_accessible TINYINT(1) NOT NULL, date DATETIME NOT NULL, oldest_known_position DATETIME NOT NULL, newest_known_position DATETIME NOT NULL, highest_delay INT NOT NULL, average_delay INT NOT NULL, company VARCHAR(255) DEFAULT NULL, vehicle_id VARCHAR(255) DEFAULT NULL, vehicle_type INT NOT NULL, positions_count INT NOT NULL, INDEX trip (trip_id), UNIQUE INDEX trip_date_unique (trip_id, date, vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE trip_statistic_data');
    }
}
