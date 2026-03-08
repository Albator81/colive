<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260308220700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE announce_equipment (announce_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_CB8E709A6F5DA3DE (announce_id), INDEX IDX_CB8E709A517FE9FE (equipment_id), PRIMARY KEY(announce_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE announce_equipment ADD CONSTRAINT FK_CB8E709A6F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE announce_equipment ADD CONSTRAINT FK_CB8E709A517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE announce_equipment DROP FOREIGN KEY FK_CB8E709A6F5DA3DE');
        $this->addSql('ALTER TABLE announce_equipment DROP FOREIGN KEY FK_CB8E709A517FE9FE');
        $this->addSql('DROP TABLE announce_equipment');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('ALTER TABLE announce ADD equipements VARCHAR(255) DEFAULT NULL');
    }
}
