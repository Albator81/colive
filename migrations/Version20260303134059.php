<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260303134059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Admin (ID 1)
        $this->addSql("INSERT INTO user (id_utilisateur, nom, prenom, email, tel, mot_de_passe, date_creation_compte, role) VALUES (1, 'Colive', 'Admin', 'admin@colive.com', '0102030405', '$2y$13$/l7MgP2gZnhurBwxJCrtFuGMs/vYEQWeNJP0hXhgYxryRgm2Miaay', NOW(), 2)");
        // Étudiant (ID 2)
        $this->addSql("INSERT INTO user (id_utilisateur, nom, prenom, email, tel, mot_de_passe, date_creation_compte, role) VALUES (2, 'User', 'Test', 'test@example.com', '0607080910', '$2y$13$4ImB5iRRVroDJpyPagZBne3BAgFxyeCtKLbffsghvmWjFHtqAPfS6', NOW(), 1)");

        // Annonce 1-2 (Admin)
        $this->addSql("INSERT INTO announce (titre, description, type, nb_pieces, prix, latitude, longitude, date_creation, disponibilite_debut, disponibilite_fin, adresse, ville, code_postal, surface, id_utilisateur) VALUES ('Studio cosy centre-ville', 'Magnifique studio refait à neuf, proche de toutes commodités.', 'Studio', 1, 550.0, 48.8566, 2.3522, NOW(), '2026-04-01', '2027-04-01', '12 Rue de Rivoli', 'Paris', '75001', 25.5, 1)");
        $this->addSql("INSERT INTO announce (titre, description, type, nb_pieces, prix, latitude, longitude, date_creation, disponibilite_debut, disponibilite_fin, adresse, ville, code_postal, surface, id_utilisateur) VALUES ('Appartement T2 moderne', 'Appartement spacieux avec balcon et garage inclus.', 'Appartement', 2, 850.0, 43.6047, 1.4442, NOW(), '2026-05-01', '2027-05-01', '10 Rue de la Pomme', 'Toulouse', '31000', 45.0, 1)");
        // Annonce 3-4 (Étudiant)
        $this->addSql("INSERT INTO announce (titre, description, type, nb_pieces, prix, latitude, longitude, date_creation, disponibilite_debut, disponibilite_fin, adresse, ville, code_postal, surface, id_utilisateur) VALUES ('Chambre en colocation lumineuse', 'Une grande chambre disponible dans un appartement de 80m2.', 'Colocation', 3, 400.0, 45.7640, 4.8357, NOW(), '2026-03-15', '2026-08-30', '45 Avenue Jean Jaurès', 'Lyon', '69007', 12.0, 2)");
        $this->addSql("INSERT INTO announce (titre, description, type, nb_pieces, prix, latitude, longitude, date_creation, disponibilite_debut, disponibilite_fin, adresse, ville, code_postal, surface, id_utilisateur) VALUES ('Studio étudiant proche fac', 'Idéal pour étudiant, meublé et équipé.', 'Studio', 1, 480.0, 44.8378, -0.5792, NOW(), '2026-09-01', '2027-06-30', '22 Cours de l\'Argonne', 'Bordeaux', '33000', 18.0, 2)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM announce WHERE id_utilisateur IN (1, 2)');
        $this->addSql('DELETE FROM user WHERE id_utilisateur IN (1, 2)');
    }
}
