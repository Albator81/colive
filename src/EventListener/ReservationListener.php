<?php

namespace App\EventListener;

use App\Entity\Reservation;
use App\Service\NotificationService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: Reservation::class)]
class ReservationListener
{
    public function __construct(
        private NotificationService $notificationService,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function onPostPersist(Reservation $Reservation): void
    {
        
    }
}
