<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200301221218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FF4F8E443');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FF4F8E443 FOREIGN KEY (prague_departure_table_id) REFERENCES prague_departure_table (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FF4F8E443');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FF4F8E443 FOREIGN KEY (prague_departure_table_id) REFERENCES prague_departure_table (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
