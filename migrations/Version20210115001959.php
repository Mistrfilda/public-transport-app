<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210115001959 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX type_finished ON request (type, finished_at)');
        $this->addSql('CREATE INDEX type_failed ON request (type, failed_at)');
        $this->addSql('CREATE INDEX finished_at ON request (finished_at)');
        $this->addSql('CREATE INDEX failed_at ON request (failed_at)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX type_finished ON request');
        $this->addSql('DROP INDEX type_failed ON request');
        $this->addSql('DROP INDEX finished_at ON request');
        $this->addSql('DROP INDEX failed_at ON request');
    }
}
