<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201013175841 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE channel (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, channel_id INT NOT NULL, channel_permalink VARCHAR(255) NOT NULL, user_id INT NOT NULL, is_watching TINYINT(1) NOT NULL, is_current TINYINT(1) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, followers_count INT DEFAULT NULL, views_count INT DEFAULT NULL, recoubs_count INT DEFAULT NULL, likes_count INT DEFAULT NULL, stories_count INT DEFAULT NULL, dislikes_count INT DEFAULT NULL, reposts_count INT DEFAULT NULL, remixes_count INT DEFAULT NULL, kd_count INT DEFAULT NULL, featured_count INT DEFAULT NULL, banned_count INT DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, timestamp DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A2F98E475DFB170D (channel_permalink), INDEX IDX_A2F98E477E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE confirmation_request (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, confirmed TINYINT(1) DEFAULT NULL, requested_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, INDEX IDX_BD9BA2CC7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, permalink VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, is_kd TINYINT(1) DEFAULT NULL, featured TINYINT(1) DEFAULT NULL, banned TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_BAA8862BE78D789C (coub_id), INDEX IDX_BAA8862B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub_stat (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, like_count INT DEFAULT NULL, repost_count INT DEFAULT NULL, remixes_count INT DEFAULT NULL, views_count INT DEFAULT NULL, dislikes_count INT DEFAULT NULL, is_kd TINYINT(1) DEFAULT NULL, featured TINYINT(1) DEFAULT NULL, banned TINYINT(1) DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_CAFF32227E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, status TINYINT(1) DEFAULT NULL, error VARCHAR(255) DEFAULT NULL, user VARCHAR(255) DEFAULT NULL, reg_code VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, channel VARCHAR(255) DEFAULT NULL, channel_id INT DEFAULT NULL, coub VARCHAR(255) DEFAULT NULL, statistic_type VARCHAR(255) DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, coub_id INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, roles JSON NOT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, token_expired_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, blocked TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE channel ADD CONSTRAINT FK_A2F98E477E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE confirmation_request ADD CONSTRAINT FK_BD9BA2CC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coub ADD CONSTRAINT FK_BAA8862B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coub_stat ADD CONSTRAINT FK_CAFF32227E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE coub DROP FOREIGN KEY FK_BAA8862B7E3C61F9');
        $this->addSql('ALTER TABLE coub_stat DROP FOREIGN KEY FK_CAFF32227E3C61F9');
        $this->addSql('ALTER TABLE channel DROP FOREIGN KEY FK_A2F98E477E3C61F9');
        $this->addSql('ALTER TABLE confirmation_request DROP FOREIGN KEY FK_BD9BA2CC7E3C61F9');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE confirmation_request');
        $this->addSql('DROP TABLE coub');
        $this->addSql('DROP TABLE coub_stat');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
    }
}
