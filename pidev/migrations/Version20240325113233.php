<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240325113233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY fk_panier_id');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY fk_user_id_credit');
        $this->addSql('ALTER TABLE digital_coins DROP FOREIGN KEY fk_investissement_dc_id');
        $this->addSql('ALTER TABLE investissement DROP FOREIGN KEY fk_user_id_actif_courant');
        $this->addSql('ALTER TABLE investissement DROP FOREIGN KEY fk_user_id_investissement');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY fk_user_id_panier');
        $this->addSql('ALTER TABLE real_estate DROP FOREIGN KEY fk_investissement_re_id');
        $this->addSql('DROP TABLE credit');
        $this->addSql('DROP TABLE depense');
        $this->addSql('DROP TABLE digital_coins');
        $this->addSql('DROP TABLE don');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE investissement');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE problem');
        $this->addSql('DROP TABLE real_estate');
        $this->addSql('DROP TABLE tax');
        $this->addSql('ALTER TABLE actif_courant CHANGE name name VARCHAR(255) NOT NULL, CHANGE montant montant DOUBLE PRECISION NOT NULL, CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE actif_non_courant DROP FOREIGN KEY fk_user_id1');
        $this->addSql('DROP INDEX fk_user_id1 ON actif_non_courant');
        $this->addSql('ALTER TABLE actif_non_courant CHANGE name name VARCHAR(255) NOT NULL, CHANGE type type VARCHAR(255) NOT NULL, CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE actif_non_courant ADD CONSTRAINT FK_F65BC069D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F65BC069D86650F ON actif_non_courant (user_id_id)');
        $this->addSql('ALTER TABLE user DROP nom, DROP prenom, DROP email, DROP numtel, DROP password, DROP adresse, DROP nbcredit, DROP rate, DROP role, DROP solde, DROP statut, DROP image, DROP datepunition');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE credit (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, panier_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, interet DOUBLE PRECISION NOT NULL, periode INT NOT NULL, INDEX fk_panier_id (panier_id), INDEX fk_user_id_credit (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE depense (id INT AUTO_INCREMENT NOT NULL, taux_tax INT DEFAULT NULL, date DATE DEFAULT NULL, type VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, montant DOUBLE PRECISION NOT NULL, user_id INT NOT NULL, INDEX fk_taux_tax (taux_tax), INDEX fk_user_id_depense (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE digital_coins (id INT AUTO_INCREMENT NOT NULL, investissement_id INT NOT NULL, code VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix_achat DOUBLE PRECISION NOT NULL, recent_value DOUBLE PRECISION NOT NULL, type VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_investissement_dc_id (investissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE don (id INT AUTO_INCREMENT NOT NULL, montant_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, user_id INT DEFAULT NULL, evenement_id INT DEFAULT NULL, INDEX user_id (user_id), INDEX evenement_id (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, nom_event VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, montant DOUBLE PRECISION NOT NULL, date DATE NOT NULL, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE investissement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, valeur_debut DOUBLE PRECISION NOT NULL, valeur_fin DOUBLE PRECISION NOT NULL, type VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, resultat VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX fk_user_id_actif_courant (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nbr_credit INT NOT NULL, INDEX fk_user_id_panier (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE problem (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, user_nom VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, error VARCHAR(250) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, timestamp VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE real_estate (id INT AUTO_INCREMENT NOT NULL, investissement_id INT NOT NULL, emplacement VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, pourcentage INT NOT NULL, roi_Annuel DOUBLE PRECISION NOT NULL, valeur DOUBLE PRECISION NOT NULL, INDEX fk_investissement_re_id (investissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tax (id INT AUTO_INCREMENT NOT NULL, montant DOUBLE PRECISION NOT NULL, type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, optimisation VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT fk_panier_id FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT fk_user_id_credit FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE digital_coins ADD CONSTRAINT fk_investissement_dc_id FOREIGN KEY (investissement_id) REFERENCES investissement (id)');
        $this->addSql('ALTER TABLE investissement ADD CONSTRAINT fk_user_id_actif_courant FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE investissement ADD CONSTRAINT fk_user_id_investissement FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT fk_user_id_panier FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE real_estate ADD CONSTRAINT fk_investissement_re_id FOREIGN KEY (investissement_id) REFERENCES investissement (id)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE actif_courant CHANGE name name VARCHAR(20) NOT NULL, CHANGE montant montant INT NOT NULL, CHANGE type type VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE actif_non_courant DROP FOREIGN KEY FK_F65BC069D86650F');
        $this->addSql('DROP INDEX IDX_F65BC069D86650F ON actif_non_courant');
        $this->addSql('ALTER TABLE actif_non_courant CHANGE name name VARCHAR(20) NOT NULL, CHANGE type type VARCHAR(50) NOT NULL, CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE actif_non_courant ADD CONSTRAINT fk_user_id1 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX fk_user_id1 ON actif_non_courant (user_id)');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(50) NOT NULL, ADD prenom VARCHAR(50) NOT NULL, ADD email VARCHAR(100) NOT NULL, ADD numtel VARCHAR(20) NOT NULL, ADD password VARCHAR(100) NOT NULL, ADD adresse VARCHAR(100) NOT NULL, ADD nbcredit INT NOT NULL, ADD rate DOUBLE PRECISION NOT NULL, ADD role VARCHAR(20) NOT NULL, ADD solde VARCHAR(200) NOT NULL, ADD statut VARCHAR(30) NOT NULL, ADD image VARCHAR(500) NOT NULL, ADD datepunition VARCHAR(50) NOT NULL');
    }
}
