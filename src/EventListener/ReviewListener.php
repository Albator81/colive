<?php

namespace App\EventListener;

use App\Entity\Review;
use App\Service\NotificationService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: Review::class)]
class ReviewListener
{
    public function __construct(
        private NotificationService $notificationService,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function onPostPersist(Review $review): void
    {
        $username = $review->getUtilisateur()->getPrenom().' '.$review->getUtilisateur()->getNom();
        $this->notificationService->createNotification(
            $review->getAnnonce()->getUtilisateur(),
            'Nouvel avis !',
            "L'utilisateur $username a émis un avis vous consernant",
            $this->router->generate('app_host_reviews', ['id' => $review->getAnnonce()->getUtilisateur()->getId()])
        );
    }
}
