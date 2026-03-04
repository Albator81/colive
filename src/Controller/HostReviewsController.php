<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HostReviewsController extends AbstractController
{
    #[Route('/hote/{id}/avis', name: 'app_host_reviews')]
    public function index(User $host): Response
    {
        return $this->render('host_reviews/index.html.twig', [
            'host' => $host,
        ]);
    }
}
