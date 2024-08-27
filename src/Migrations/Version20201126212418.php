<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201126212418 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE coub_banned_count (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, banned INT DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_7FC4C0837E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub_dislikes_count (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, dislikes_count INT DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_EAEDCEFE7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub_featured_count (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, featured INT DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_FAA7D2497E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub_kd_count (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, is_kd INT DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_5D5C7A767E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub_like_count (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, like_count INT DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_71A06B37E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub_remixes_count (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, remixes_count INT DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_8D1171197E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub_repost_count (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, repost_count INT DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_30135F877E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coub_views_count (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, coub_id INT NOT NULL, channel_id INT NOT NULL, views_count INT DEFAULT NULL, date_create DATETIME DEFAULT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_4CAFAB9D7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coub_banned_count ADD CONSTRAINT FK_7FC4C0837E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coub_dislikes_count ADD CONSTRAINT FK_EAEDCEFE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coub_featured_count ADD CONSTRAINT FK_FAA7D2497E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coub_kd_count ADD CONSTRAINT FK_5D5C7A767E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coub_like_count ADD CONSTRAINT FK_71A06B37E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coub_remixes_count ADD CONSTRAINT FK_8D1171197E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coub_repost_count ADD CONSTRAINT FK_30135F877E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coub_views_count ADD CONSTRAINT FK_4CAFAB9D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES channel (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE coub_banned_count');
        $this->addSql('DROP TABLE coub_dislikes_count');
        $this->addSql('DROP TABLE coub_featured_count');
        $this->addSql('DROP TABLE coub_kd_count');
        $this->addSql('DROP TABLE coub_like_count');
        $this->addSql('DROP TABLE coub_remixes_count');
        $this->addSql('DROP TABLE coub_repost_count');
        $this->addSql('DROP TABLE coub_views_count');
    }
}
