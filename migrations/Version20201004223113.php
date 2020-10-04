<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201004223113 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prague_parking_lot (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', parking_id VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, address VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, payment_url VARCHAR(255) DEFAULT NULL, INDEX parking_id (parking_id), INDEX address (address), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prague_parking_lot_occupancy (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', parking_lot_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', total_spaces INT NOT NULL, free_spaces INT NOT NULL, occupied_spaces INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_247DCDA0FCDC866A (parking_lot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prague_parking_lot_occupancy ADD CONSTRAINT FK_247DCDA0FCDC866A FOREIGN KEY (parking_lot_id) REFERENCES prague_parking_lot (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prague_parking_lot_occupancy DROP FOREIGN KEY FK_247DCDA0FCDC866A');
        $this->addSql('DROP TABLE prague_parking_lot');
        $this->addSql('DROP TABLE prague_parking_lot_occupancy');
    }
}
