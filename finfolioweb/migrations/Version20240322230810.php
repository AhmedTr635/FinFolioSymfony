<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322230810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evennement (id INT AUTO_INCREMENT NOT NULL, nom_event VARCHAR(255) NOT NULL, montant DOUBLE PRECISION DEFAULT NULL, date DATETIME NOT NULL, adresse VARCHAR(255) NOT NULL, description VARCHAR(5000) DEFAULT NULL, rating INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_id (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE actif_non_courant DROP FOREIGN KEY fk_user_id1');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY fk_user_id_credit');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY fk_panier_id');
        $this->addSql('ALTER TABLE digital_coins DROP FOREIGN KEY fk_investissement_dc_id');
        $this->addSql('ALTER TABLE investissement DROP FOREIGN KEY fk_user_id_actif_courant');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY fk_user_id_panier');
        $this->addSql('ALTER TABLE real_estate DROP FOREIGN KEY fk_investissement_re_id');
        $this->addSql('DROP TABLE actif_courant');
        $this->addSql('DROP TABLE actif_non_courant');
        $this->addSql('DROP TABLE credit');
        $this->addSql('DROP TABLE depense');
        $this->addSql('DROP TABLE digital_coins');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE investissement');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE real_estate');
        $this->addSql('DROP TABLE tax');
        $this->addSql('DROP INDEX user_id ON don');
        $this->addSql('DROP INDEX evenement_id ON don');
        $this->addSql('ALTER TABLE don ADD user_id_id INT DEFAULT NULL, ADD evenement_id_id INT DEFAULT NULL, DROP user_id, DROP evenement_id, CHANGE montant_user montant_user DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D99D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D9ECEE32AF FOREIGN KEY (evenement_id_id) REFERENCES evennement (id)');
        $this->addSql('CREATE INDEX IDX_F8F081D99D86650F ON don (user_id_id)');
        $this->addSql('CREATE INDEX IDX_F8F081D9ECEE32AF ON don (evenement_id_id)');
        $this->addSql('ALTER TABLE user ADD total_tax DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D9ECEE32AF');
        $this->addSql('CREATE TABLE actif_courant (id INT AUTO_INCREMENT NOT NULL, montant INT NOT NULL, type VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, user_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE actif_non_courant (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, valeur DOUBLE PRECISION NOT NULL, prix_achat DOUBLE PRECISION NOT NULL, INDEX fk_user_id1 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE credit (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, panier_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, interet DOUBLE PRECISION NOT NULL, periode INT NOT NULL, INDEX fk_panier_id (panier_id), INDEX fk_user_id_credit (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE depense (id INT AUTO_INCREMENT NOT NULL, taux_tax INT DEFAULT NULL, date DATE DEFAULT NULL, type VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, montant DOUBLE PRECISION NOT NULL, user_id INT NOT NULL, INDEX fk_taux_tax (taux_tax), INDEX fk_user_id_depense (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE digital_coins (id INT AUTO_INCREMENT NOT NULL, investissement_id INT NOT NULL, code VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix_achat DOUBLE PRECISION NOT NULL, recent_value DOUBLE PRECISION NOT NULL, type VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_investissement_dc_id (investissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, nom_event VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, montant DOUBLE PRECISION NOT NULL, date DATE NOT NULL, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description MEDIUMTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, rating INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE investissement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, valeur_debut DOUBLE PRECISION NOT NULL, valeur_fin DOUBLE PRECISION NOT NULL, type VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, resultat VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_user_id_actif_courant (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nbr_credit INT NOT NULL, INDEX fk_user_id_panier (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE real_estate (id INT AUTO_INCREMENT NOT NULL, investissement_id INT NOT NULL, emplacement VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, pourcentage INT NOT NULL, roi_Annuel DOUBLE PRECISION NOT NULL, valeur DOUBLE PRECISION NOT NULL, INDEX fk_investissement_re_id (investissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tax (id INT AUTO_INCREMENT NOT NULL, montant DOUBLE PRECISION NOT NULL, type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, optimisation VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE actif_non_courant ADD CONSTRAINT fk_user_id1 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT fk_user_id_credit FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT fk_panier_id FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE digital_coins ADD CONSTRAINT fk_investissement_dc_id FOREIGN KEY (investissement_id) REFERENCES investissement (id)');
        $this->addSql('ALTER TABLE investissement ADD CONSTRAINT fk_user_id_actif_courant FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT fk_user_id_panier FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE real_estate ADD CONSTRAINT fk_investissement_re_id FOREIGN KEY (investissement_id) REFERENCES investissement (id)');
        $this->addSql('DROP TABLE evennement');
        $this->addSql('DROP TABLE user_id');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D99D86650F');
        $this->addSql('DROP INDEX IDX_F8F081D99D86650F ON don');
        $this->addSql('DROP INDEX IDX_F8F081D9ECEE32AF ON don');
        $this->addSql('ALTER TABLE don ADD user_id INT DEFAULT NULL, ADD evenement_id INT DEFAULT NULL, DROP user_id_id, DROP evenement_id_id, CHANGE montant_user montant_user VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX user_id ON don (user_id)');
        $this->addSql('CREATE INDEX evenement_id ON don (evenement_id)');
        $this->addSql('ALTER TABLE user DROP total_tax');
    }
}
