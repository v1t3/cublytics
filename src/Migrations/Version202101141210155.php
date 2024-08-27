<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202101141210155 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('INSERT INTO user (user_id, roles, username, email, password, token, token_expired_at, created_at, updated_at, date_create, date_update, blocked) VALUES (124, \'["ROLE_ADMIN", "ROLE_USER"]\', \'test\', \'test@test.com\', \'$2y$13$KAQ9LbuKAy0xTwK2b8R87u3qb1CF2uPBq9IKelUtd.askWIg8i5jG\', null, null, null, null, null, \'2024-08-23 14:57:29\', null)');
        $this->addSql('INSERT INTO confirmation_request (owner_id, code, confirmed, requested_at, expires_at) VALUES (1, null, 1, null, null)');

        $this->addSql("INSERT INTO channel (owner_id, channel_id, channel_permalink, user_id, is_watching, is_current, title, avatar, followers_count, views_count, recoubs_count, likes_count, stories_count, dislikes_count, reposts_count, remixes_count, kd_count, featured_count, banned_count, is_active, timestamp, created_at, updated_at, deleted_at, date_create, date_update) VALUES (1, 23456, 'https://coub.com/super.couber', 12345, 1, 1, 'СуперКоубер', 'https://3fc4ed44-3fbc-419a-97a1-a29742511391.selcdn.net/coub_storage/channel/cw_avatar/d62190870aa/5bf4374017bf38406c747/profile_pic_big_1610394229_1N78ZyipteM.jpg', 700, 5405, 0, 47641, 1, 128, 126, 19, 1, 1, 0, 1, null, '2018-08-10 20:08:44', '2018-08-10 20:08:44', null, null, null)");

        $this->addSql("INSERT INTO coub_views_count (owner_id, coub_id, channel_id, views_count, date_create, date_update) VALUES (2, 123, 23456, 100, '2024-08-19 11:18:05', '2024-08-19 11:18:05')");
        $this->addSql("INSERT INTO coub_views_count (owner_id, coub_id, channel_id, views_count, date_create, date_update) VALUES (2, 123, 23456, 300, '2024-08-20 11:18:05', '2024-08-20 11:18:05')");
        $this->addSql("INSERT INTO coub_views_count (owner_id, coub_id, channel_id, views_count, date_create, date_update) VALUES (2, 123, 23456, 440, '2024-08-21 11:18:05', '2024-08-21 11:18:05')");
        $this->addSql("INSERT INTO coub_views_count (owner_id, coub_id, channel_id, views_count, date_create, date_update) VALUES (2, 123, 23456, 500, '2024-08-22 11:18:05', '2024-08-22 11:18:05')");
        $this->addSql("INSERT INTO coub_views_count (owner_id, coub_id, channel_id, views_count, date_create, date_update) VALUES (2, 123, 23456, 700, '2024-08-23 11:18:05', '2024-08-23 11:18:05')");
        $this->addSql("INSERT INTO coub_views_count (owner_id, coub_id, channel_id, views_count, date_create, date_update) VALUES (2, 123, 23456, 750, '2024-08-24 11:18:05', '2024-08-24 11:18:05')");
        $this->addSql("INSERT INTO coub_views_count (owner_id, coub_id, channel_id, views_count, date_create, date_update) VALUES (2, 123, 23456, 800, '2024-08-25 11:18:05', '2024-08-25 11:18:05')");
        $this->addSql("INSERT INTO coub_views_count (owner_id, coub_id, channel_id, views_count, date_create, date_update) VALUES (2, 123, 23456, 900, '2024-08-26 11:18:05', '2024-08-26 11:18:05')");
        $this->addSql("INSERT INTO coub_views_count (owner_id, coub_id, channel_id, views_count, date_create, date_update) VALUES (2, 123, 23456, 915, '2024-08-27 11:18:05', '2024-08-27 11:18:05')");

        $this->addSql("INSERT INTO coub_like_count (owner_id, coub_id, channel_id, like_count, date_create, date_update) VALUES (2, 123, 23456, 1123, '2024-08-19 11:18:05', '2024-08-19 11:18:05')");
        $this->addSql("INSERT INTO coub_like_count (owner_id, coub_id, channel_id, like_count, date_create, date_update) VALUES (2, 123, 23456, 2001, '2024-08-20 11:18:05', '2024-08-20 11:18:05')");
        $this->addSql("INSERT INTO coub_like_count (owner_id, coub_id, channel_id, like_count, date_create, date_update) VALUES (2, 123, 23456, 3012, '2024-08-21 11:18:05', '2024-08-21 11:18:05')");
        $this->addSql("INSERT INTO coub_like_count (owner_id, coub_id, channel_id, like_count, date_create, date_update) VALUES (2, 123, 23456, 4200, '2024-08-22 11:18:05', '2024-08-22 11:18:05')");
        $this->addSql("INSERT INTO coub_like_count (owner_id, coub_id, channel_id, like_count, date_create, date_update) VALUES (2, 123, 23456, 5200, '2024-08-23 11:18:05', '2024-08-23 11:18:05')");
        $this->addSql("INSERT INTO coub_like_count (owner_id, coub_id, channel_id, like_count, date_create, date_update) VALUES (2, 123, 23456, 5300, '2024-08-24 11:18:05', '2024-08-24 11:18:05')");
        $this->addSql("INSERT INTO coub_like_count (owner_id, coub_id, channel_id, like_count, date_create, date_update) VALUES (2, 123, 23456, 6000, '2024-08-25 11:18:05', '2024-08-25 11:18:05')");
        $this->addSql("INSERT INTO coub_like_count (owner_id, coub_id, channel_id, like_count, date_create, date_update) VALUES (2, 123, 23456, 8800, '2024-08-26 11:18:05', '2024-08-26 11:18:05')");
        $this->addSql("INSERT INTO coub_like_count (owner_id, coub_id, channel_id, like_count, date_create, date_update) VALUES (2, 123, 23456, 12005, '2024-08-27 11:18:05', '2024-08-27 11:18:05')");

        $this->addSql("INSERT INTO coub_dislikes_count (owner_id, coub_id, channel_id, dislikes_count, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-19 11:18:05', '2024-08-19 11:18:05')");
        $this->addSql("INSERT INTO coub_dislikes_count (owner_id, coub_id, channel_id, dislikes_count, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-20 11:18:05', '2024-08-20 11:18:05')");
        $this->addSql("INSERT INTO coub_dislikes_count (owner_id, coub_id, channel_id, dislikes_count, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-21 11:18:05', '2024-08-21 11:18:05')");
        $this->addSql("INSERT INTO coub_dislikes_count (owner_id, coub_id, channel_id, dislikes_count, date_create, date_update) VALUES (2, 123, 23456, 9, '2024-08-22 11:18:05', '2024-08-22 11:18:05')");
        $this->addSql("INSERT INTO coub_dislikes_count (owner_id, coub_id, channel_id, dislikes_count, date_create, date_update) VALUES (2, 123, 23456, 10, '2024-08-23 11:18:05', '2024-08-23 11:18:05')");
        $this->addSql("INSERT INTO coub_dislikes_count (owner_id, coub_id, channel_id, dislikes_count, date_create, date_update) VALUES (2, 123, 23456, 25, '2024-08-24 11:18:05', '2024-08-24 11:18:05')");
        $this->addSql("INSERT INTO coub_dislikes_count (owner_id, coub_id, channel_id, dislikes_count, date_create, date_update) VALUES (2, 123, 23456, 25, '2024-08-25 11:18:05', '2024-08-25 11:18:05')");
        $this->addSql("INSERT INTO coub_dislikes_count (owner_id, coub_id, channel_id, dislikes_count, date_create, date_update) VALUES (2, 123, 23456, 27, '2024-08-26 11:18:05', '2024-08-26 11:18:05')");
        $this->addSql("INSERT INTO coub_dislikes_count (owner_id, coub_id, channel_id, dislikes_count, date_create, date_update) VALUES (2, 123, 23456, 32, '2024-08-27 11:18:05', '2024-08-27 11:18:05')");

        $this->addSql("INSERT INTO coub_repost_count (owner_id, coub_id, channel_id, repost_count, date_create, date_update) VALUES (2, 123, 23456, 2, '2024-08-19 11:18:05', '2024-08-19 11:18:05')");
        $this->addSql("INSERT INTO coub_repost_count (owner_id, coub_id, channel_id, repost_count, date_create, date_update) VALUES (2, 123, 23456, 5, '2024-08-20 11:18:05', '2024-08-20 11:18:05')");
        $this->addSql("INSERT INTO coub_repost_count (owner_id, coub_id, channel_id, repost_count, date_create, date_update) VALUES (2, 123, 23456, 10, '2024-08-21 11:18:05', '2024-08-21 11:18:05')");
        $this->addSql("INSERT INTO coub_repost_count (owner_id, coub_id, channel_id, repost_count, date_create, date_update) VALUES (2, 123, 23456, 11, '2024-08-22 11:18:05', '2024-08-22 11:18:05')");
        $this->addSql("INSERT INTO coub_repost_count (owner_id, coub_id, channel_id, repost_count, date_create, date_update) VALUES (2, 123, 23456, 15, '2024-08-23 11:18:05', '2024-08-23 11:18:05')");
        $this->addSql("INSERT INTO coub_repost_count (owner_id, coub_id, channel_id, repost_count, date_create, date_update) VALUES (2, 123, 23456, 20, '2024-08-24 11:18:05', '2024-08-24 11:18:05')");
        $this->addSql("INSERT INTO coub_repost_count (owner_id, coub_id, channel_id, repost_count, date_create, date_update) VALUES (2, 123, 23456, 20, '2024-08-25 11:18:05', '2024-08-25 11:18:05')");
        $this->addSql("INSERT INTO coub_repost_count (owner_id, coub_id, channel_id, repost_count, date_create, date_update) VALUES (2, 123, 23456, 21, '2024-08-26 11:18:05', '2024-08-26 11:18:05')");
        $this->addSql("INSERT INTO coub_repost_count (owner_id, coub_id, channel_id, repost_count, date_create, date_update) VALUES (2, 123, 23456, 22, '2024-08-27 11:18:05', '2024-08-27 11:18:05')");

        $this->addSql("INSERT INTO coub_remixes_count (owner_id, coub_id, channel_id, remixes_count, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-19 11:18:05', '2024-08-19 11:18:05')");
        $this->addSql("INSERT INTO coub_remixes_count (owner_id, coub_id, channel_id, remixes_count, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-20 11:18:05', '2024-08-20 11:18:05')");
        $this->addSql("INSERT INTO coub_remixes_count (owner_id, coub_id, channel_id, remixes_count, date_create, date_update) VALUES (2, 123, 23456, 1, '2024-08-21 11:18:05', '2024-08-21 11:18:05')");
        $this->addSql("INSERT INTO coub_remixes_count (owner_id, coub_id, channel_id, remixes_count, date_create, date_update) VALUES (2, 123, 23456, 2, '2024-08-22 11:18:05', '2024-08-22 11:18:05')");
        $this->addSql("INSERT INTO coub_remixes_count (owner_id, coub_id, channel_id, remixes_count, date_create, date_update) VALUES (2, 123, 23456, 2, '2024-08-23 11:18:05', '2024-08-23 11:18:05')");
        $this->addSql("INSERT INTO coub_remixes_count (owner_id, coub_id, channel_id, remixes_count, date_create, date_update) VALUES (2, 123, 23456, 3, '2024-08-24 11:18:05', '2024-08-24 11:18:05')");
        $this->addSql("INSERT INTO coub_remixes_count (owner_id, coub_id, channel_id, remixes_count, date_create, date_update) VALUES (2, 123, 23456, 3, '2024-08-25 11:18:05', '2024-08-25 11:18:05')");
        $this->addSql("INSERT INTO coub_remixes_count (owner_id, coub_id, channel_id, remixes_count, date_create, date_update) VALUES (2, 123, 23456, 4, '2024-08-26 11:18:05', '2024-08-26 11:18:05')");
        $this->addSql("INSERT INTO coub_remixes_count (owner_id, coub_id, channel_id, remixes_count, date_create, date_update) VALUES (2, 123, 23456, 4, '2024-08-27 11:18:05', '2024-08-27 11:18:05')");

        $this->addSql("INSERT INTO coub_kd_count (owner_id, coub_id, channel_id, is_kd, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-19 11:18:05', '2024-08-19 11:18:05')");
        $this->addSql("INSERT INTO coub_kd_count (owner_id, coub_id, channel_id, is_kd, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-20 11:18:05', '2024-08-20 11:18:05')");
        $this->addSql("INSERT INTO coub_kd_count (owner_id, coub_id, channel_id, is_kd, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-21 11:18:05', '2024-08-21 11:18:05')");
        $this->addSql("INSERT INTO coub_kd_count (owner_id, coub_id, channel_id, is_kd, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-22 11:18:05', '2024-08-22 11:18:05')");
        $this->addSql("INSERT INTO coub_kd_count (owner_id, coub_id, channel_id, is_kd, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-23 11:18:05', '2024-08-23 11:18:05')");
        $this->addSql("INSERT INTO coub_kd_count (owner_id, coub_id, channel_id, is_kd, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-24 11:18:05', '2024-08-24 11:18:05')");
        $this->addSql("INSERT INTO coub_kd_count (owner_id, coub_id, channel_id, is_kd, date_create, date_update) VALUES (2, 123, 23456, 1, '2024-08-25 11:18:05', '2024-08-25 11:18:05')");
        $this->addSql("INSERT INTO coub_kd_count (owner_id, coub_id, channel_id, is_kd, date_create, date_update) VALUES (2, 123, 23456, 1, '2024-08-26 11:18:05', '2024-08-26 11:18:05')");
        $this->addSql("INSERT INTO coub_kd_count (owner_id, coub_id, channel_id, is_kd, date_create, date_update) VALUES (2, 123, 23456, 1, '2024-08-27 11:18:05', '2024-08-27 11:18:05')");

        $this->addSql("INSERT INTO coub_featured_count (owner_id, coub_id, channel_id, featured, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-19 11:18:05', '2024-08-19 11:18:05')");
        $this->addSql("INSERT INTO coub_featured_count (owner_id, coub_id, channel_id, featured, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-20 11:18:05', '2024-08-20 11:18:05')");
        $this->addSql("INSERT INTO coub_featured_count (owner_id, coub_id, channel_id, featured, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-21 11:18:05', '2024-08-21 11:18:05')");
        $this->addSql("INSERT INTO coub_featured_count (owner_id, coub_id, channel_id, featured, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-22 11:18:05', '2024-08-22 11:18:05')");
        $this->addSql("INSERT INTO coub_featured_count (owner_id, coub_id, channel_id, featured, date_create, date_update) VALUES (2, 123, 23456, 0, '2024-08-23 11:18:05', '2024-08-23 11:18:05')");
        $this->addSql("INSERT INTO coub_featured_count (owner_id, coub_id, channel_id, featured, date_create, date_update) VALUES (2, 123, 23456, 1, '2024-08-24 11:18:05', '2024-08-24 11:18:05')");
        $this->addSql("INSERT INTO coub_featured_count (owner_id, coub_id, channel_id, featured, date_create, date_update) VALUES (2, 123, 23456, 1, '2024-08-25 11:18:05', '2024-08-25 11:18:05')");
        $this->addSql("INSERT INTO coub_featured_count (owner_id, coub_id, channel_id, featured, date_create, date_update) VALUES (2, 123, 23456, 1, '2024-08-26 11:18:05', '2024-08-26 11:18:05')");
        $this->addSql("INSERT INTO coub_featured_count (owner_id, coub_id, channel_id, featured, date_create, date_update) VALUES (2, 123, 23456, 1, '2024-08-27 11:18:05', '2024-08-27 11:18:05')");
    }

    public function down(Schema $schema) : void
    {
    }
}
