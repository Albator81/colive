<?php

namespace App\Factory;

use App\Entity\Equipment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class EquipmentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Equipment::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'nom' => self::faker()->unique()->randomElement([
                'WiFi',
                'Machine à laver',
                'Sèche-linge',
                'Lave-vaisselle',
                'Réfrigérateur',
                'Four',
                'Micro-ondes',
                'Climatisation',
                'Chauffage',
                'Télévision',
                'Canapé',
                'Lit',
                'Bureau',
                'Armoire',
                'Salle de bain privative',
                'Balcon',
                'Terrasse',
                'Parking',
                'Ascenseur',
                'Piscine',
            ]),
        ];
    }
}
