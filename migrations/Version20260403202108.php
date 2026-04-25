<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260403202108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announce_user_equipment (announce_id INT NOT NULL, user_equipment_id INT NOT NULL, INDEX IDX_87555E586F5DA3DE (announce_id), INDEX IDX_87555E58D1B395FC (user_equipment_id), PRIMARY KEY (announce_id, user_equipment_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_equipment (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, id_utilisateur INT NOT NULL, INDEX IDX_D3D8586750EAE44 (id_utilisateur), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE announce_user_equipment ADD CONSTRAINT FK_87555E586F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE announce_user_equipment ADD CONSTRAINT FK_87555E58D1B395FC FOREIGN KEY (user_equipment_id) REFERENCES user_equipment (id)');
        $this->addSql('ALTER TABLE user_equipment ADD CONSTRAINT FK_D3D8586750EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce_user_equipment DROP FOREIGN KEY FK_87555E586F5DA3DE');
        $this->addSql('ALTER TABLE announce_user_equipment DROP FOREIGN KEY FK_87555E58D1B395FC');
        $this->addSql('ALTER TABLE user_equipment DROP FOREIGN KEY FK_D3D8586750EAE44');
        $this->addSql('DROP TABLE announce_user_equipment');
        $this->addSql('DROP TABLE user_equipment');
    }
}
