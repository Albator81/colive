<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: "review")]
#[ApiResource(
    operations: [
        new GetCollection(
            order: ['note' => 'DESC'],
            normalizationContext: ['groups' => ['rewiew_read']]
            ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['annonce.utilisateur.id' => 'exact'])]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_avis", type: "integer")]
    #[Groups(['rewiew_read'])]
    private ?int $id = null;

    #[ORM\Column(type: "integer")]
    #[Groups(['rewiew_read'])]
    private int $note;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['rewiew_read'])]
    private ?string $commentaire = null;

    #[ORM\Column(name: "date_creation", type: "datetime")]
    #[Groups(['rewiew_read'])]
    private \DateTimeInterface $dateCreation;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'avis')]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    #[Groups(['rewiew_read'])]
    private ?User $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Announce::class, inversedBy: 'avis')]
    #[ORM\JoinColumn(name: "id_annonce", referencedColumnName: "id_annonce", nullable: false)]
    private ?Announce $annonce = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getNote(): int { return $this->note; }
    public function setNote(int $n): self { $this->note = $n; return $this; }
    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $c): self { $this->commentaire = $c; return $this; }
    public function getDateCreation(): \DateTimeInterface { return $this->dateCreation; }
    public function getUtilisateur(): ?User { return $this->utilisateur; }
    public function setUtilisateur(?User $u): self { $this->utilisateur = $u; return $this; }
    public function getAnnonce(): ?Announce { return $this->annonce; }
    public function setAnnonce(?Announce $a): self { $this->annonce = $a; return $this; }
}
