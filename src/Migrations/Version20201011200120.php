<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201011200120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE channel ADD CONSTRAINT FK_A2F98E47A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2F98E4772F5A1AA ON channel (channel_id)');
        $this->addSql('CREATE INDEX IDX_A2F98E47A76ED395 ON channel (user_id)');
        $this->addSql('ALTER TABLE coub CHANGE channel_id channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coub ADD CONSTRAINT FK_BAA8862B72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (channel_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_BAA8862B72F5A1AA ON coub (channel_id)');
        $this->addSql('ALTER TABLE coub_stat CHANGE channel_id channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coub_stat ADD CONSTRAINT FK_CAFF322272F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (channel_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_CAFF322272F5A1AA ON coub_stat (channel_id)');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel DROP FOREIGN KEY FK_A2F98E47A76ED395');
        $this->addSql('DROP INDEX UNIQ_A2F98E4772F5A1AA ON channel');
        $this->addSql('DROP INDEX IDX_A2F98E47A76ED395 ON channel');
        $this->addSql('ALTER TABLE channel CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE coub DROP FOREIGN KEY FK_BAA8862B72F5A1AA');
        $this->addSql('DROP INDEX IDX_BAA8862B72F5A1AA ON coub');
        $this->addSql('ALTER TABLE coub CHANGE channel_id channel_id INT NOT NULL');
        $this->addSql('ALTER TABLE coub_stat DROP FOREIGN KEY FK_CAFF322272F5A1AA');
        $this->addSql('DROP INDEX IDX_CAFF322272F5A1AA ON coub_stat');
        $this->addSql('ALTER TABLE coub_stat CHANGE channel_id channel_id INT NOT NULL');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
