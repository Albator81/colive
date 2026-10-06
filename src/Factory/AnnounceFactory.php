<?php

namespace App\Factory;

use App\Entity\Announce;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class AnnounceFactory extends PersistentProxyObjectFactory
{
    private const TITRES = [
        'Studio' => [
            'Studio lumineux proche Part-Dieu',
            'Studio meublé à deux pas du campus',
            'Studio rénové quartier Guillotière',
            'Studio calme avec balcon',
            'Studio tout équipé proche métro',
        ],
        'Chambre' => [
            'Chambre meublée chez l\'habitant',
            'Grande chambre proche métro',
            'Chambre étudiante tout confort',
            'Chambre dans maison avec jardin',
            'Chambre au calme proche des écoles',
        ],
        'Appartement' => [
            'T2 cosy en plein centre',
            'Appartement T3 avec terrasse',
            'T2 refait à neuf proche gare',
            'Appartement lumineux vue Fourvière',
            'Bel appartement meublé à la Croix-Rousse',
        ],
        'Collocation' => [
            'Colocation à 3 dans grand T4',
            'Colocation étudiante conviviale',
            'Chambre en colocation proche tram',
            'Colocation entre alternants',
            'Grande colocation avec salon partagé',
        ],
    ];

    private const ACCROCHES = [
        'Logement entièrement meublé et prêt à vivre, idéal pour un rythme école / entreprise.',
        'Situé dans un quartier vivant, à quelques minutes à pied des transports et des commerces.',
        'Logement calme et lumineux, parfait pour réviser comme pour se détendre après le travail.',
        'Récemment rénové, avec des rangements et un vrai espace bureau.',
    ];

    private const DETAILS = [
        'Charges comprises : eau, électricité et internet fibre.',
        'Cuisine équipée (plaques, four, réfrigérateur) et machine à laver à disposition.',
        'Bail flexible, adapté aux périodes d\'alternance.',
        'Local à vélos et laverie dans l\'immeuble.',
        'Métro et tram à moins de cinq minutes, gare accessible en un quart d\'heure.',
        'Propriétaire disponible et réactif, état des lieux simplifié.',
    ];

    private const REGLES = [
        'Non fumeur',
        'Animaux non acceptés',
        'Pas de soirées en semaine',
        'Calme après 22h',
        'Ménage des parties communes à tour de rôle',
    ];

    public static function class(): string
    {
        return Announce::class;
    }

    protected function defaults(): array|callable
    {
        $type = self::faker()->randomElement(array_keys(self::TITRES));

        return [
            'titre' => self::faker()->randomElement(self::TITRES[$type]),
            'description' => self::faker()->randomElement(self::ACCROCHES).' '.implode(' ', self::faker()->randomElements(self::DETAILS, 3)),
            'type' => $type,
            'nb_pieces' => self::faker()->numberBetween(1, 4),
            'prix' => self::faker()->numberBetween(35, 95) * 10,
            'regle' => self::faker()->randomElement(self::REGLES),
            'latitude' => self::faker()->latitude(45.7, 45.8),
            'longitude' => self::faker()->longitude(4.8, 4.9),
            'adresse' => self::faker()->streetAddress(),
            'ville' => 'Lyon',
            'code_postal' => '6900'.self::faker()->numberBetween(1, 9),
            'surface' => self::faker()->numberBetween(12, 60),
            'disponibilite_debut' => self::faker()->dateTimeBetween('now', '+1 month'),
            'disponibilite_fin' => self::faker()->dateTimeBetween('+6 months', '+1 year'),
            'utilisateur' => UserFactory::new(),
            'equipment' => EquipmentFactory::randomRange(1, 5),
            'isValidated' => self::faker()->boolean(70),
        ];
    }
}
