<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        $equipment = $em->getRepository(Equipment::class)->findAll();

        return $this->render('home/index.html.twig', [
            'equipment' => $equipment,
        ]);
    }

    #[Route('/test', name: 'app_test_mercure')]
    public function testSend(NotificationService $notificationService): Response
    {
        $user = $this->getUser();

        if ($user) {
            $notificationService->createNotification(
                $user,
                'titre de qualite',
                'petite notification',
                '/a/a'
            );

            return new Response('notification envoye !');
        }

        return new Response('t pas connecte');
    }
}
