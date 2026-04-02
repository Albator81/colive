<?php

namespace App\Controller;

use App\Entity\UserEquipment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EquipmentController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/api/user-equipment', name: 'app_equipment_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['nom'] ?? null;

        if (!$name || trim($name) === '') {
            return $this->json(['error' => 'Le nom est requis'], 400);
        }

        $name = trim($name);

        if (preg_match('/[<>&"]/', $name)) {
            return $this->json(['error' => 'Caractères interdits détectés'], 400);
        }

        $user = $this->getUser();
        $existing = $em->getRepository(UserEquipment::class)->findOneBy(['nom' => $name, 'utilisateur' => $user]);
        if ($existing) {
            return $this->json(['id' => $existing->getId(), 'nom' => $existing->getNom()]);
        }

        $userEquipment = new UserEquipment();
        $userEquipment->setNom($name);
        $userEquipment->setUtilisateur($user);
        $em->persist($userEquipment);
        $em->flush();

        return $this->json(['id' => $userEquipment->getId(), 'nom' => $userEquipment->getNom()]);
    }
}
