<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class NotificationController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/notification/clear', name: 'app_notification_clear')]
    public function clear(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user && $user instanceof User) {
            foreach ($user->getNotifications() as $notification) {
                $entityManager->remove($notification);
            }
            $entityManager->flush();
        }

        return new Response('Fait', Response::HTTP_OK);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/notification/see', name: 'app_notification_see')]
    public function see(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user && $user instanceof User) {
            foreach ($user->getNotifications() as $notification) {
                if (!$notification->isSeen()) {
                    $notification->setIsSeen(true);
                }
            }
            $entityManager->flush();
        }

        return new Response('Fait', Response::HTTP_OK);
    }
}
