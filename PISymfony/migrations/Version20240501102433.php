<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240501102433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE digital_coins CHANGE recent_value recent_value DOUBLE PRECISION NOT NULL, CHANGE roi roi DOUBLE PRECISION NOT NULL, CHANGE tax tax DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE investissement CHANGE re_id re_id INT NOT NULL');
        $this->addSql('ALTER TABLE real_estate ADD growth DOUBLE PRECISION NOT NULL, ADD longitude DOUBLE PRECISION NOT NULL, ADD latitude DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE digital_coins CHANGE recent_value recent_value DOUBLE PRECISION DEFAULT NULL, CHANGE roi roi DOUBLE PRECISION DEFAULT NULL, CHANGE tax tax DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE investissement CHANGE re_id re_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE real_estate DROP growth, DROP longitude, DROP latitude');
    }
}
