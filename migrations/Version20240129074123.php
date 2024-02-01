<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129074123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notifications_users (id INT AUTO_INCREMENT NOT NULL, is_read TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifications_users_user (notifications_users_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E9F1CD07BAFB555C (notifications_users_id), INDEX IDX_E9F1CD07A76ED395 (user_id), PRIMARY KEY(notifications_users_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifications_users_notification (notifications_users_id INT NOT NULL, notification_id INT NOT NULL, INDEX IDX_CF1411FDBAFB555C (notifications_users_id), INDEX IDX_CF1411FDEF1A9D84 (notification_id), PRIMARY KEY(notifications_users_id, notification_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notifications_users_user ADD CONSTRAINT FK_E9F1CD07BAFB555C FOREIGN KEY (notifications_users_id) REFERENCES notifications_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications_users_user ADD CONSTRAINT FK_E9F1CD07A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications_users_notification ADD CONSTRAINT FK_CF1411FDBAFB555C FOREIGN KEY (notifications_users_id) REFERENCES notifications_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications_users_notification ADD CONSTRAINT FK_CF1411FDEF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notifications_users_user DROP FOREIGN KEY FK_E9F1CD07BAFB555C');
        $this->addSql('ALTER TABLE notifications_users_user DROP FOREIGN KEY FK_E9F1CD07A76ED395');
        $this->addSql('ALTER TABLE notifications_users_notification DROP FOREIGN KEY FK_CF1411FDBAFB555C');
        $this->addSql('ALTER TABLE notifications_users_notification DROP FOREIGN KEY FK_CF1411FDEF1A9D84');
        $this->addSql('DROP TABLE notifications_users');
        $this->addSql('DROP TABLE notifications_users_user');
        $this->addSql('DROP TABLE notifications_users_notification');
    }
}
