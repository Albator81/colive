<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AnnounceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AnnounceRepository::class)]
#[ORM\Table(name: 'announce')]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['announce_read']],
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['ville' => 'partial', 'type' => 'exact', 'equipment' => 'exact'])]
#[ApiFilter(DateFilter::class, properties: ['disponibilite_debut', 'disponibilite_fin'])]
#[ApiFilter(RangeFilter::class, properties: ['prix', 'surface'])]
class Announce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_annonce', type: 'integer')]
    #[Groups(['announce_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['announce_read'])]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['announce_read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['announce_read'])]
    private ?string $type = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['announce_read'])]
    private ?int $nb_pieces = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['announce_read'])]
    private ?float $prix = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['announce_read'])]
    private ?string $regle = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['announce_read'])]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(type: 'date')]
    #[Groups(['announce_read'])]
    private \DateTimeInterface $disponibilite_debut;

    #[ORM\Column(type: 'date')]
    #[Groups(['announce_read'])]
    private \DateTimeInterface $disponibilite_fin;

    #[ORM\Column(length: 255)]
    #[Groups(['announce_read'])]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Groups(['announce_read'])]
    private ?string $ville = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['announce_read'])]
    private ?string $code_postal = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['announce_read'])]
    private ?float $surface = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['announce_read'])]
    private bool $isValidated = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'annonces')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    #[Groups(['announce_read'])]
    private ?User $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: Review::class, cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['announce_read'])]
    private Collection $avis;

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: AnnouncePicture::class, cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['announce_read'])]
    private Collection $photos;

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: UserLikes::class, cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['announce_read'])]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'announce', targetEntity: Reservation::class, cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['announce_read'])]
    private Collection $reservations;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6)]
    #[Groups(['announce_read'])]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6)]
    #[Groups(['announce_read'])]
    private ?string $longitude = null;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'annonces')]
    #[ORM\JoinTable(name: 'announce_equipment')]
    #[ORM\JoinColumn(name: 'announce_id', referencedColumnName: 'id_annonce')]
    #[ORM\InverseJoinColumn(name: 'equipment_id', referencedColumnName: 'id')]
    #[Groups(['announce_read'])]
    private Collection $equipment;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->disponibilite_debut = new \DateTime();
        $this->disponibilite_fin = new \DateTime();
        $this->avis = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->isValidated = false;
        $this->equipment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $t): self
    {
        $this->titre = $t;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $d): self
    {
        $this->description = $d;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $t): self
    {
        $this->type = $t;

        return $this;
    }

    public function getNbPieces(): ?int
    {
        return $this->nb_pieces;
    }

    public function setNbPieces(int $n): self
    {
        $this->nb_pieces = $n;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $p): self
    {
        $this->prix = $p;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $l): self
    {
        $this->latitude = $l;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $l): self
    {
        $this->longitude = $l;

        return $this;
    }

    public function getRegle(): ?string
    {
        return $this->regle;
    }

    public function setRegle(?string $r): self
    {
        $this->regle = $r;

        return $this;
    }

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $d): self
    {
        $this->dateCreation = $d;

        return $this;
    }

    public function getDisponibiliteDebut(): \DateTimeInterface
    {
        return $this->disponibilite_debut;
    }

    public function setDisponibiliteDebut(\DateTimeInterface $d): self
    {
        $this->disponibilite_debut = $d;

        return $this;
    }

    public function getDisponibiliteFin(): \DateTimeInterface
    {
        return $this->disponibilite_fin;
    }

    public function setDisponibiliteFin(\DateTimeInterface $d): self
    {
        $this->disponibilite_fin = $d;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $a): self
    {
        $this->adresse = $a;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $v): self
    {
        $this->ville = $v;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(?string $cp): self
    {
        $this->code_postal = $cp;

        return $this;
    }

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(?float $s): self
    {
        $this->surface = $s;

        return $this;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $u): self
    {
        $this->utilisateur = $u;

        return $this;
    }

    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setAnnounce($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getAnnounce() === $this) {
                $reservation->setAnnounce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
            $equipment->addAnnonce($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        if ($this->equipment->removeElement($equipment)) {
            $equipment->removeAnnonce($this);
        }

        return $this;
    }
}
