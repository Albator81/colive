<?php

namespace App\EventListener;

use App\Entity\Reservation;
use App\Service\NotificationService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: Reservation::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'onPostUpdate', entity: Reservation::class)]
class ReservationListener
{
    public function __construct(
        private NotificationService $notificationService,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function onPostPersist(Reservation $reservation): void
    {
        $username = $reservation->getLocataire()->getPrenom().' '.$reservation->getLocataire()->getNom();
        $this->notificationService->createNotification($reservation->getAnnounce()->getUtilisateur(), 'Nouvelle réservaition', "$username à émis une demande de réservation pour l'une de vos annonces", $this->router->generate('app_message_conversation', ['id' => $reservation->getLocataire()->getId()]));
    }

    public function onPostUpdate(Reservation $reservation): void
    {
        $username = $reservation->getLocataire()->getPrenom().' '.$reservation->getLocataire()->getNom();
        $announceName = $reservation->getAnnounce()->getTitre();

        if ('CANCELLED' == $reservation->getStatut()) {
            $this->notificationService->createNotification(
                $reservation->getLocataire(),
                'Annulation de la réservation',
                "Votre réservation pour l'annonce $announceName à été annulée/refusée !",
                $this->router->generate('app_message_conversation', ['id' => $reservation->getAnnounce()->getUtilisateur()->getId()])
            );

            $this->notificationService->createNotification(
                $reservation->getAnnounce()->getUtilisateur(),
                'Annulation de la réservation',
                "La réservation de l'annonce $announceName avec l'utilisateur $username à été annulée/refusée",
                $this->router->generate('app_message_conversation', ['id' => $reservation->getLocataire()->getId()])
            );
        } elseif ('CONFIRMED' == $reservation->getStatut()) {
            $this->notificationService->createNotification(
                $reservation->getLocataire(),
                'Confirmation de la réservation',
                "Votre réservation pour l'annonce $announceName à été confirmée !",
                $this->router->generate('app_message_conversation', ['id' => $reservation->getAnnounce()->getUtilisateur()->getId()])
            );

            $this->notificationService->createNotification(
                $reservation->getAnnounce()->getUtilisateur(),
                'Confirmation de la réservation',
                "La réservation de l'annonce $announceName avec l'utilisateur $username à été confirmée !",
                $this->router->generate('app_message_conversation', ['id' => $reservation->getLocataire()->getId()])
            );
        }
    }
}
