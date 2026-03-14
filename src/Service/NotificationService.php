<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HubInterface $hub,
    ) {
    }

    public function createNotification(User $user, string $title, string $content, string $target): Notification
    {
        $notification = new Notification();
        $notification->setTitle($title)
            ->setContent($content)
            ->setIsSeen(false)
            ->setTargetLink($target)
            ->setTimestamp(new \DateTime())
            ->setTarget($user);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $update = new Update(
            $user->getNotificationTopic()->toString(),
            json_encode([
                'title' => $notification->getTitle(),
                'content' => $notification->getContent(),
                'target' => $target,
            ])
        );

        $this->hub->publish($update);

        return $notification;
    }
}
