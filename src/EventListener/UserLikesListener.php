<?php

namespace App\EventListener;

use App\Entity\UserLikes;
use App\Service\NotificationService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: UserLikes::class)]
class UserLikesListener
{
    public function __construct(
        private NotificationService $notificationService,
        private UrlGeneratorInterface $router
    ) {}

    public function onPostPersist(UserLikes $like): void
    {
        $username = $like->getUtilisateur()->getPrenom() . ' ' . $like->getUtilisateur()->getNom();
        $this->notificationService->createNotification($like->getAnnonce()->getUtilisateur(), "Nouveau like !", "L'utilisateur $username a liké une de vos annonces !", $this->router->generate('app_announce_show', ['id' => $like->getAnnonce()->getId()]));
    }
}