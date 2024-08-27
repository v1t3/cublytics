<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201022194819 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE coub_authorization (id INT AUTO_INCREMENT NOT NULL, last_username VARCHAR(255) NOT NULL, token VARCHAR(255) DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_943096DF803F43E9 (last_username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE coub_authorization');
    }
}
