<?php

namespace App\EventListener;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: Notification::class)]
class NotificationListener
{
    public function __construct(
        private HubInterface $hub
    ) {}

    public function onPostPersist(Notification $notification): void
    {
        $target = $notification->getTarget();
        
        if ($target) {
            $update = new Update(
                $target->getNotificationTopic()->toString(),
                json_encode([
                    'title' => $notification->getTitle(),
                    'content' => $notification->getContent(),
                ])
            );

            $this->hub->publish($update);
        }
    }
}