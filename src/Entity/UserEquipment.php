<?php

namespace App\Entity;

use App\Repository\UserEquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserEquipmentRepository::class)]
class UserEquipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['announce_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['announce_read'])]
    #[Assert\Regex('/^[^<>&"]*$/', message: 'Le nom de l\'équipement contient des caractères interdits.')]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'userEquipments')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?User $utilisateur = null;

    #[ORM\ManyToMany(targetEntity: Announce::class, mappedBy: 'userEquipments')]
    private Collection $annonces;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Announce $annonce): static
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->addUserEquipment($this);
        }

        return $this;
    }

    public function removeAnnonce(Announce $annonce): static
    {
        if ($this->annonces->removeElement($annonce)) {
            $annonce->removeUserEquipment($this);
        }

        return $this;
    }
}
