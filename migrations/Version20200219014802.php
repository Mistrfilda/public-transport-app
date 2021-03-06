<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200219014802 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prague_stop_time (id INT AUTO_INCREMENT NOT NULL, stop_id INT DEFAULT NULL, arrival_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', departure_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', trip_id VARCHAR(255) NOT NULL, stop_sequence INT NOT NULL, INDEX IDX_495B7E873902063D (stop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prague_stop_time ADD CONSTRAINT FK_495B7E873902063D FOREIGN KEY (stop_id) REFERENCES prague_stop (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE prague_stop_time');
    }
}
