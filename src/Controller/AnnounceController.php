<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\AnnouncePicture;
use App\Entity\Equipment;
use App\Entity\UserLikes;
use App\Form\AnnounceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AnnounceController extends AbstractController
{
    #[Route('/announce', name: 'app_announce')]
    public function index(EntityManagerInterface $em)
    {
        $equipment = $em->getRepository(Equipment::class)->findAll();

        return $this->render('announce/index.html.twig', [
            'equipment' => $equipment,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/announce/create', name: 'app_announce_create')]
    public function create(Request $request, EntityManagerInterface $em, HttpClientInterface $httpClient): Response
    {
        $annonce = new Announce();
        $form = $this->createForm(AnnounceType::class, $annonce, ['user' => $this->getUser()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setUtilisateur($this->getUser());
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                $fileContent = file_get_contents($image->getPathname());
                $base64 = base64_encode($fileContent);
                $mimeType = $image->getMimeType();
                $dataUri = 'data:'.$mimeType.';base64,'.$base64;
                $picture = new AnnouncePicture();
                $picture->setContenu($dataUri);
                $picture->setAnnonce($annonce);
                $em->persist($picture);
            }
            $this->setCoordinates($annonce, $httpClient);
            $em->persist($annonce);
            $em->flush();
            $this->addFlash('success', 'Votre annonce a été publiée avec succès.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('announce/create.html.twig', [
            'formAnnonce' => $form->createView(),
        ]);
    }

    private function setCoordinates(Announce $annonce, HttpClientInterface $httpClient): void
    {
        $response = $httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
            'verify_peer' => false,
            'query' => [
                'street' => $annonce->getAdresse(),
                'city' => $annonce->getVille(),
                'format' => 'json',
                'limit' => 1,
            ],
            'headers' => [
                'User-Agent' => 'WAAAA/1.0 (set-contact-mail-for-prod@gmail.com)',
            ],
        ]);

        $data = $response->toArray();

        if (!empty($data)) {
            $annonce->setLatitude($data[0]['lat']);
            $annonce->setLongitude($data[0]['lon']);
        } else {
            $annonce->setLatitude(.0);
            $annonce->setLongitude(.0);
        }
    }

    #[Route('/announce/{id}/like', name: 'app_announce_like')]
    public function like(Announce $announce, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'Non autorisé'], 403);
        }
        $like = $entityManager->getRepository(UserLikes::class)->findOneBy([
            'utilisateur' => $user,
            'annonce' => $announce,
        ]);
        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();

            return $this->json(['isLiked' => false]);
        }
        $newLike = new UserLikes();
        $newLike->setUtilisateur($user);
        $newLike->setAnnonce($announce);

        $entityManager->persist($newLike);
        $entityManager->flush();

        return $this->json(['isLiked' => true]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/announce/{id}/edit', name: 'app_announce_edit')]
    public function edit(Announce $annonce, Request $request, EntityManagerInterface $em, HttpClientInterface $httpClient): Response
    {
        if ($annonce->getUtilisateur() !== $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier cette annonce.');

            return $this->redirectToRoute('app_profile');
        }

        $form = $this->createForm(AnnounceType::class, $annonce, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                $fileContent = file_get_contents($image->getPathname());
                $base64 = base64_encode($fileContent);
                $mimeType = $image->getMimeType();
                $dataUri = 'data:'.$mimeType.';base64,'.$base64;

                $picture = new AnnouncePicture();
                $picture->setContenu($dataUri);
                $picture->setAnnonce($annonce);
                $em->persist($picture);
            }
            $this->setCoordinates($annonce, $httpClient);
            $em->flush();

            $this->addFlash('success', 'Votre annonce a été mise à jour.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('announce/edit.html.twig', [
            'formAnnonce' => $form->createView(),
            'annonce' => $annonce,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/announce/picture/{id}/delete', name: 'app_announce_picture_delete', methods: ['DELETE'])]
    public function deletePicture(AnnouncePicture $picture, EntityManagerInterface $em): JsonResponse
    {
        $annonce = $picture->getAnnonce();

        if ($annonce->getUtilisateur() !== $this->getUser()) {
            return $this->json(['error' => 'Action non autorisée'], 403);
        }

        $em->remove($picture);
        $em->flush();

        return $this->json(['success' => true]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/announce/{id}/delete', name: 'app_announce_delete', methods: ['POST'])]
    public function delete(Announce $annonce, Request $request, EntityManagerInterface $em): Response
    {
        if ($annonce->getUtilisateur() !== $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer une annonce qui ne vous appartient pas.');

            return $this->redirectToRoute('app_profile');
        }
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $em->remove($annonce);
            $em->flush();

            $this->addFlash('success', 'L\'annonce a été supprimée avec succès.');
        } else {
            $this->addFlash('danger', 'Token de sécurité invalide.');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/announce/{id}', name: 'app_announce_show')]
    public function show(Announce $announce): Response
    {
        return $this->render('announce/show.html.twig', [
            'announce' => $announce,
        ]);
    }
}
