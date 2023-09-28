<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230925164553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_notification (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE check_list (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, fragrance_id INT DEFAULT NULL, state VARCHAR(255) NOT NULL, delete_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A1488C99A76ED395 (user_id), INDEX IDX_A1488C99A6F3CE8E (fragrance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE code_validation (id INT AUTO_INCREMENT NOT NULL, source VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, expired_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_exclusive (id INT AUTO_INCREMENT NOT NULL, tag_id INT DEFAULT NULL, image_src VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, desktop_pdf VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, audio VARCHAR(255) DEFAULT NULL, link LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_31C450FFBAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fragrance (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, brand VARCHAR(255) NOT NULL, img LONGTEXT NOT NULL, create_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', concentration VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE layering (id INT AUTO_INCREMENT NOT NULL, fragrance1_id INT DEFAULT NULL, fragrance2_id INT DEFAULT NULL, user_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', create_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1CC0A392DCCA707A (fragrance1_id), INDEX IDX_1CC0A392CE7FDF94 (fragrance2_id), INDEX IDX_1CC0A392A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE my_favorite_types_of_perfumes (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_8FD2379BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, category_notification_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, total_send INT NOT NULL, notification_open INT NOT NULL, description LONGTEXT DEFAULT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BF5476CAB793C1D6 (category_notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_user (notification_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_35AF9D73EF1A9D84 (notification_id), INDEX IDX_35AF9D73A76ED395 (user_id), PRIMARY KEY(notification_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE perfume_trial_sheet (id INT AUTO_INCREMENT NOT NULL, fragrance_id INT DEFAULT NULL, user_id INT DEFAULT NULL, dominant_notes LONGTEXT DEFAULT NULL, evolution_of_perfume LONGTEXT DEFAULT NULL, more LONGTEXT DEFAULT NULL, less LONGTEXT DEFAULT NULL, delete_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', impression LONGTEXT DEFAULT NULL, INDEX IDX_8876551EA6F3CE8E (fragrance_id), INDEX IDX_8876551EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, my_symbolic_fragrance_id INT DEFAULT NULL, user_id INT NOT NULL, notes_to_discover VARCHAR(255) DEFAULT NULL, childhood_scents VARCHAR(255) DEFAULT NULL, felt_on_my_collection TINYINT(1) DEFAULT NULL, INDEX IDX_E6D6B297A56C8D8C (my_symbolic_fragrance_id), UNIQUE INDEX UNIQ_E6D6B297A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review_perfume_note (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, review LONGTEXT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delete_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F8467ABBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review_perfume_note_fragrance (review_perfume_note_id INT NOT NULL, fragrance_id INT NOT NULL, INDEX IDX_C8984DC2E525A1E0 (review_perfume_note_id), INDEX IDX_C8984DC2A6F3CE8E (fragrance_id), PRIMARY KEY(review_perfume_note_id, fragrance_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(13) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, id_client_stripe VARCHAR(255) DEFAULT NULL, subscription_at DATETIME DEFAULT NULL, type_subscription INT NOT NULL, id_subscription_stripe VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649444F97DD (phone), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, fragrance_id INT DEFAULT NULL, user_id INT NOT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delete_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9CE12A31A6F3CE8E (fragrance_id), INDEX IDX_9CE12A31A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE check_list ADD CONSTRAINT FK_A1488C99A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE check_list ADD CONSTRAINT FK_A1488C99A6F3CE8E FOREIGN KEY (fragrance_id) REFERENCES fragrance (id)');
        $this->addSql('ALTER TABLE content_exclusive ADD CONSTRAINT FK_31C450FFBAD26311 FOREIGN KEY (tag_id) REFERENCES content_tag (id)');
        $this->addSql('ALTER TABLE layering ADD CONSTRAINT FK_1CC0A392DCCA707A FOREIGN KEY (fragrance1_id) REFERENCES fragrance (id)');
        $this->addSql('ALTER TABLE layering ADD CONSTRAINT FK_1CC0A392CE7FDF94 FOREIGN KEY (fragrance2_id) REFERENCES fragrance (id)');
        $this->addSql('ALTER TABLE layering ADD CONSTRAINT FK_1CC0A392A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE my_favorite_types_of_perfumes ADD CONSTRAINT FK_8FD2379BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB793C1D6 FOREIGN KEY (category_notification_id) REFERENCES category_notification (id)');
        $this->addSql('ALTER TABLE notification_user ADD CONSTRAINT FK_35AF9D73EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_user ADD CONSTRAINT FK_35AF9D73A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perfume_trial_sheet ADD CONSTRAINT FK_8876551EA6F3CE8E FOREIGN KEY (fragrance_id) REFERENCES fragrance (id)');
        $this->addSql('ALTER TABLE perfume_trial_sheet ADD CONSTRAINT FK_8876551EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297A56C8D8C FOREIGN KEY (my_symbolic_fragrance_id) REFERENCES fragrance (id)');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review_perfume_note ADD CONSTRAINT FK_F8467ABBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review_perfume_note_fragrance ADD CONSTRAINT FK_C8984DC2E525A1E0 FOREIGN KEY (review_perfume_note_id) REFERENCES review_perfume_note (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review_perfume_note_fragrance ADD CONSTRAINT FK_C8984DC2A6F3CE8E FOREIGN KEY (fragrance_id) REFERENCES fragrance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31A6F3CE8E FOREIGN KEY (fragrance_id) REFERENCES fragrance (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE check_list DROP FOREIGN KEY FK_A1488C99A76ED395');
        $this->addSql('ALTER TABLE check_list DROP FOREIGN KEY FK_A1488C99A6F3CE8E');
        $this->addSql('ALTER TABLE content_exclusive DROP FOREIGN KEY FK_31C450FFBAD26311');
        $this->addSql('ALTER TABLE layering DROP FOREIGN KEY FK_1CC0A392DCCA707A');
        $this->addSql('ALTER TABLE layering DROP FOREIGN KEY FK_1CC0A392CE7FDF94');
        $this->addSql('ALTER TABLE layering DROP FOREIGN KEY FK_1CC0A392A76ED395');
        $this->addSql('ALTER TABLE my_favorite_types_of_perfumes DROP FOREIGN KEY FK_8FD2379BA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAB793C1D6');
        $this->addSql('ALTER TABLE notification_user DROP FOREIGN KEY FK_35AF9D73EF1A9D84');
        $this->addSql('ALTER TABLE notification_user DROP FOREIGN KEY FK_35AF9D73A76ED395');
        $this->addSql('ALTER TABLE perfume_trial_sheet DROP FOREIGN KEY FK_8876551EA6F3CE8E');
        $this->addSql('ALTER TABLE perfume_trial_sheet DROP FOREIGN KEY FK_8876551EA76ED395');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B297A56C8D8C');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B297A76ED395');
        $this->addSql('ALTER TABLE review_perfume_note DROP FOREIGN KEY FK_F8467ABBA76ED395');
        $this->addSql('ALTER TABLE review_perfume_note_fragrance DROP FOREIGN KEY FK_C8984DC2E525A1E0');
        $this->addSql('ALTER TABLE review_perfume_note_fragrance DROP FOREIGN KEY FK_C8984DC2A6F3CE8E');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31A6F3CE8E');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31A76ED395');
        $this->addSql('DROP TABLE category_notification');
        $this->addSql('DROP TABLE check_list');
        $this->addSql('DROP TABLE code_validation');
        $this->addSql('DROP TABLE content_exclusive');
        $this->addSql('DROP TABLE content_tag');
        $this->addSql('DROP TABLE fragrance');
        $this->addSql('DROP TABLE layering');
        $this->addSql('DROP TABLE my_favorite_types_of_perfumes');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_user');
        $this->addSql('DROP TABLE perfume_trial_sheet');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE review_perfume_note');
        $this->addSql('DROP TABLE review_perfume_note_fragrance');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
