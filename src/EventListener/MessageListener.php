<?php

namespace App\EventListener;

use App\Entity\Message;
use App\Service\NotificationService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: Message::class)]
class MessageListener
{
    public function __construct(
        private NotificationService $notificationService,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function onPostPersist(Message $message): void
    {
        $username = $message->getSender()->getPrenom().' '.$message->getSender()->getNom();
        $this->notificationService->createNotification(
            $message->getRecipient(), 
            'Nouveau message !', 
            "Vous avez reçu un nouveau message de la part de $username", 
            $this->router->generate('app_message_conversation', ['id' => $message->getSender()->getId()])
        );
    }
}
