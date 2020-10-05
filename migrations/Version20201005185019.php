<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201005185019 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prague_parking_lot ADD last_parking_lot_occupancy_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE prague_parking_lot ADD CONSTRAINT FK_90AD6BF9F44558BD FOREIGN KEY (last_parking_lot_occupancy_id) REFERENCES prague_parking_lot_occupancy (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_90AD6BF9F44558BD ON prague_parking_lot (last_parking_lot_occupancy_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prague_parking_lot DROP FOREIGN KEY FK_90AD6BF9F44558BD');
        $this->addSql('DROP INDEX UNIQ_90AD6BF9F44558BD ON prague_parking_lot');
        $this->addSql('ALTER TABLE prague_parking_lot DROP last_parking_lot_occupancy_id');
    }
}
