<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130110005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournament CHANGE nbr_participant nbr_participant INT DEFAULT NULL, CHANGE etat etat INT DEFAULT NULL, CHANGE result result VARCHAR(255) DEFAULT NULL');
        $this->addSql("INSERT INTO `user` (`id`, `name`, `roles`, `password`, `pseudo`) 
            VALUES (1, 'Admin', '[\"ROLE_USER\", \"ROLE_ADMIN\"]', '$2y$13$oAwbiOl5T3ftrwJliyjzoeu.ybMDEx/xOuAobhlAgk42yM9UrA.WG', 'Le Surpuissant');");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournament CHANGE nbr_participant nbr_participant INT NOT NULL, CHANGE etat etat INT NOT NULL, CHANGE result result VARCHAR(255) NOT NULL');
    }
}
