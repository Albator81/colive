<?php

namespace App\DataFixtures;

use App\Factory\AnnounceFactory;
use App\Factory\AnnouncePictureFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AnnouncePictureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach (AnnounceFactory::all() as $announce) {
            foreach (AnnouncePictureFactory::randomImages(rand(2, 4)) as $image) {
                AnnouncePictureFactory::createOne(['annonce' => $announce, 'contenu' => $image]);
            }
        }
    }

    public function getDependencies(): array
    {
        return [AnnounceFixtures::class];
    }
}
