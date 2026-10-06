<?php

namespace App\Factory;

use App\Entity\Review;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ReviewFactory extends PersistentProxyObjectFactory
{
    private const COMMENTAIRES = [
        'Logement conforme à l\'annonce, propre et bien situé. Je recommande.',
        'Propriétaire très réactif, emménagement simple et rapide.',
        'Parfait pour mon alternance : à dix minutes de l\'école et de l\'entreprise.',
        'Très bonne colocation, ambiance studieuse et conviviale.',
        'Quartier calme et bien desservi, rien à redire.',
        'Appartement lumineux et bien équipé, quelques travaux de peinture à prévoir.',
        'Bon rapport qualité-prix pour le secteur.',
        'Séjour agréable, le logement est fonctionnel et bien isolé.',
        'Un peu bruyant côté rue, mais l\'emplacement est idéal.',
        'Hôte arrangeant sur les dates, ce qui est précieux en alternance.',
    ];

    public static function class(): string
    {
        return Review::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'note' => self::faker()->numberBetween(3, 5),
            'commentaire' => self::faker()->randomElement(self::COMMENTAIRES),
            'utilisateur' => UserFactory::new(),
            'annonce' => AnnounceFactory::new(),
        ];
    }
}
