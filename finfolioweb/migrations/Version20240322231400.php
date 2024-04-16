<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322231400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_id');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D9ECEE32AF');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D99D86650F');
        $this->addSql('DROP INDEX IDX_F8F081D99D86650F ON don');
        $this->addSql('DROP INDEX IDX_F8F081D9ECEE32AF ON don');
        $this->addSql('ALTER TABLE don ADD user_id_id INT DEFAULT NULL, ADD evenement_id_id INT DEFAULT NULL, DROP user_id, DROP evenement_id');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D9ECEE32AF FOREIGN KEY (evenement_id_id) REFERENCES evennement (id)');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D99D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F8F081D99D86650F ON don (user_id_id)');
        $this->addSql('CREATE INDEX IDX_F8F081D9ECEE32AF ON don (evenement_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_id (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D99D86650F');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D9ECEE32AF');
        $this->addSql('DROP INDEX IDX_F8F081D99D86650F ON don');
        $this->addSql('DROP INDEX IDX_F8F081D9ECEE32AF ON don');
        $this->addSql('ALTER TABLE don ADD user_id INT DEFAULT NULL, ADD evenement_id INT DEFAULT NULL, DROP user_id_id, DROP evenement_id_id');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D99D86650F FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D9ECEE32AF FOREIGN KEY (evenement_id) REFERENCES evennement (id)');
        $this->addSql('CREATE INDEX IDX_F8F081D99D86650F ON don (user_id)');
        $this->addSql('CREATE INDEX IDX_F8F081D9ECEE32AF ON don (evenement_id)');
    }
}
