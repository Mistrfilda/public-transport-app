<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200221132520 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prague_vehicle_position (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', city VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prague_vehicle (id INT AUTO_INCREMENT NOT NULL, vehicle_position_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', route_id VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, final_station VARCHAR(255) NOT NULL, delay_in_seconds INT NOT NULL, wheelchair_accessible TINYINT(1) NOT NULL, last_stop_id VARCHAR(255) DEFAULT NULL, next_stop_id VARCHAR(255) DEFAULT NULL, INDEX IDX_271AF03453BF5330 (vehicle_position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prague_vehicle ADD CONSTRAINT FK_271AF03453BF5330 FOREIGN KEY (vehicle_position_id) REFERENCES prague_vehicle_position (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prague_vehicle DROP FOREIGN KEY FK_271AF03453BF5330');
        $this->addSql('DROP TABLE prague_vehicle_position');
        $this->addSql('DROP TABLE prague_vehicle');
    }
}
