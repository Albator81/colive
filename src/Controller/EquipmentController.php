<?php

namespace App\Controller;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EquipmentController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/api/equipment', name: 'app_equipment_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['nom'] ?? null;

        if (!$name || trim($name) === '') {
            return $this->json(['error' => 'Le nom est requis'], 400);
        }

        $existing = $em->getRepository(Equipment::class)->findOneBy(['nom' => $name]);
        if ($existing) {
            return $this->json(['id' => $existing->getId(), 'nom' => $existing->getNom()]);
        }

        $equipment = new Equipment();
        $equipment->setNom(trim($name));
        $em->persist($equipment);
        $em->flush();

        return $this->json(['id' => $equipment->getId(), 'nom' => $equipment->getNom()]);
    }
}
