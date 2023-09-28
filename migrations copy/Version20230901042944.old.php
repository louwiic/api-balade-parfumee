<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230901042944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE layering (id INT AUTO_INCREMENT NOT NULL, fragrance1_id INT DEFAULT NULL, fragrance2_id INT DEFAULT NULL, user_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', create_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1CC0A392DCCA707A (fragrance1_id), INDEX IDX_1CC0A392CE7FDF94 (fragrance2_id), INDEX IDX_1CC0A392A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE layering ADD CONSTRAINT FK_1CC0A392DCCA707A FOREIGN KEY (fragrance1_id) REFERENCES fragrance (id)');
        $this->addSql('ALTER TABLE layering ADD CONSTRAINT FK_1CC0A392CE7FDF94 FOREIGN KEY (fragrance2_id) REFERENCES fragrance (id)');
        $this->addSql('ALTER TABLE layering ADD CONSTRAINT FK_1CC0A392A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE layering DROP FOREIGN KEY FK_1CC0A392DCCA707A');
        $this->addSql('ALTER TABLE layering DROP FOREIGN KEY FK_1CC0A392CE7FDF94');
        $this->addSql('ALTER TABLE layering DROP FOREIGN KEY FK_1CC0A392A76ED395');
        $this->addSql('DROP TABLE layering');
    }
}
