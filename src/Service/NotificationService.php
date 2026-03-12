<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function createNotification(User $user, string $title, string $content): Notification
    {
        $notification = new Notification();
        $notification->setTitle($title)
            ->setContent($content)
            ->setIsSeen(false)
            ->setTarget($user);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }
}