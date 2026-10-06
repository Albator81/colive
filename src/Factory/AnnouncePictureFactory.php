<?php

namespace App\Factory;

use App\Entity\AnnouncePicture;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class AnnouncePictureFactory extends PersistentProxyObjectFactory
{
    private const IMAGES_DIR = __DIR__.'/../DataFixtures/images';
    private const PLACEHOLDER = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    public static function class(): string
    {
        return AnnouncePicture::class;
    }

    /**
     * Picks $count distinct sample photos (bundled with the fixtures) as base64 data URIs.
     *
     * @return string[]
     */
    public static function randomImages(int $count = 1): array
    {
        $files = glob(self::IMAGES_DIR.'/*.jpg') ?: [];
        if ([] === $files) {
            return array_fill(0, $count, self::PLACEHOLDER);
        }

        return array_map(
            static fn (string $file): string => 'data:image/jpeg;base64,'.base64_encode(file_get_contents($file)),
            self::faker()->randomElements($files, min($count, count($files)))
        );
    }

    protected function defaults(): array|callable
    {
        return [
            'contenu' => self::randomImages()[0],
            'annonce' => AnnounceFactory::new(),
            'dateCreation' => self::faker()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
