<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Notification;
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

    #[Route('/test', name: 'app_send')]
    public function testSend(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user){
            $notif = new Notification();
            $notif->setTitle("titre aaa")
                ->setContent("boooo")
                ->setIsSeen(false)
                ->setTarget($user);
            $entityManager->persist($notif);
            $entityManager->flush();
        }

        return new Response("fait");
    }
}
