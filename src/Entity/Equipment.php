<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
    ]
)]
#[UniqueEntity(fields: ['nom'], message: 'Cet équipement existe déjà dans la base de données.')]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['announce_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['announce_read'])]
    private ?string $nom = null;

    /**
     * @var Collection<int, Announce>
     */
    #[ORM\ManyToMany(targetEntity: Announce::class, mappedBy: 'equipment')]
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

    /**
     * @return Collection<int, Announce>
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Announce $annonce): static
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
        }

        return $this;
    }

    public function removeAnnonce(Announce $annonce): static
    {
        $this->annonces->removeElement($annonce);

        return $this;
    }
}
