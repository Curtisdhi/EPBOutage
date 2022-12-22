<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221222211733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boundaries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, hash_sum VARCHAR(255) NOT NULL, boundaries_json LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outage (id INT UNSIGNED NOT NULL, boundaries_id INT UNSIGNED DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', updated_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', importer_version VARCHAR(255) NOT NULL, auto_restored_outages INT NOT NULL, current_outages INT NOT NULL, customers_affected INT NOT NULL, crew_dispatched INT NOT NULL, duration_outages INT NOT NULL, prevented_outages INT NOT NULL, total_smart_grid_activity INT NOT NULL, smart_grid_restores INT NOT NULL, manual_restores INT NOT NULL, start_datetime DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', end_date_time DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', incidents LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', district_incidents LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', full_json LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_C37E8DF8D17F50A6 (uuid), INDEX IDX_C37E8DF883DC14EC (boundaries_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE outage ADD CONSTRAINT FK_C37E8DF883DC14EC FOREIGN KEY (boundaries_id) REFERENCES boundaries (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE outage DROP FOREIGN KEY FK_C37E8DF883DC14EC');
        $this->addSql('DROP TABLE boundaries');
        $this->addSql('DROP TABLE outage');
    }
}
