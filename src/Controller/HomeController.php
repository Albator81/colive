<?php

namespace App\Controller;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
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

    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        return $this->render('home/test.html.twig');
    }

    #[Route('/test_send', name: 'app_send')]
    public function testSend(HubInterface $interface): Response
    {
        $interface->publish(
            new Update(
                "boo",
                json_encode(['status' => 'OutOfStock'])
            )
        );
        return new Response("aaaaaaaaaaa");
    }
}
