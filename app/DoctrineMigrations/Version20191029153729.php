<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20191029153729 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE outlet (id INT AUTO_INCREMENT NOT NULL, outlet_name VARCHAR(255) NOT NULL, building_name VARCHAR(100) DEFAULT NULL, property_number VARCHAR(20) NOT NULL, street_name VARCHAR(200) NOT NULL, area VARCHAR(200) NOT NULL, town VARCHAR(200) NOT NULL, postcode VARCHAR(30) NOT NULL, country VARCHAR(200) NOT NULL, contact_number VARCHAR(50) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE outlet');
    }
}
