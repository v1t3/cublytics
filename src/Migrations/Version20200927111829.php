<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200927111829 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT NULL, error VARCHAR(255) DEFAULT NULL, user VARCHAR(255) DEFAULT NULL, reg_code VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, channel VARCHAR(255) DEFAULT NULL, channel_id INT DEFAULT NULL, coub VARCHAR(255) DEFAULT NULL, statistic_type VARCHAR(255) DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, coub_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE log');
    }
}
