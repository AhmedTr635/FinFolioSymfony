<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329232339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE digital_coins (id INT AUTO_INCREMENT NOT NULL, recent_value DOUBLE PRECISION NOT NULL, date_achat DATE NOT NULL, date_vente DATE DEFAULT NULL, montant INT NOT NULL, leverage INT NOT NULL, stop_loss DOUBLE PRECISION NOT NULL, user_id INT NOT NULL, roi DOUBLE PRECISION NOT NULL, prix_achat DOUBLE PRECISION NOT NULL, tax DOUBLE PRECISION NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE investissement (id INT AUTO_INCREMENT NOT NULL, date_achat DATE NOT NULL, prix_achat INT NOT NULL, roi INT NOT NULL, montant INT NOT NULL, tax DOUBLE PRECISION NOT NULL, re_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE real_estate (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, emplacement VARCHAR(255) NOT NULL, roi DOUBLE PRECISION NOT NULL, valeur DOUBLE PRECISION NOT NULL, nbrchambres INT NOT NULL, superficie DOUBLE PRECISION NOT NULL, nbrclick INT DEFAULT NULL, image_data VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, montant INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE digital_coins');
        $this->addSql('DROP TABLE investissement');
        $this->addSql('DROP TABLE real_estate');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
