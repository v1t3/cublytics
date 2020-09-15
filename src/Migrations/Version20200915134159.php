<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200915134159 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE channel (id INT AUTO_INCREMENT NOT NULL, channel_id INT NOT NULL, channel_permalink VARCHAR(255) NOT NULL, user_id INT NOT NULL, is_watching TINYINT(1) NOT NULL, is_current TINYINT(1) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, followers_count INT DEFAULT NULL, recoubs_count INT DEFAULT NULL, likes_count INT DEFAULT NULL, stories_count INT DEFAULT NULL, views_count INT DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, timestamp DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A2F98E475DFB170D (channel_permalink), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub (id INT AUTO_INCREMENT NOT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, permalink VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_BAA8862BE78D789C (coub_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub_stat (id INT AUTO_INCREMENT NOT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, like_count INT DEFAULT NULL, repost_count INT DEFAULT NULL, remixes_count INT DEFAULT NULL, views_count INT DEFAULT NULL, dislikes_count INT DEFAULT NULL, is_kd TINYINT(1) DEFAULT NULL, featured TINYINT(1) DEFAULT NULL, banned TINYINT(1) DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, roles JSON NOT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, token_expired_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE coub');
        $this->addSql('DROP TABLE coub_stat');
        $this->addSql('DROP TABLE user');
    }
}
