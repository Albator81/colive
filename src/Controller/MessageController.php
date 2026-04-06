<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MessageController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/message', name: 'app_message')]
    #[Route('/message/{id}', name: 'app_message_conversation')]
    public function index(?int $id, MessageRepository $messageRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $allContacts = $currentUser->getContacts();
        $searchTerm = $request->query->get('q');
        $users = [];

        if ($searchTerm) {
            foreach ($allContacts as $contact) {
                if (false !== stripos($contact->getNom(), $searchTerm)
                    || false !== stripos($contact->getPrenom(), $searchTerm)) {
                    $users[] = $contact;
                }
            }
        } else {
            $users = $allContacts;
        }
        $selectedUser = null;
        $messages = [];

        if ($id) {
            $selectedUser = $entityManager->getRepository(User::class)->find($id);
            $r = $request->headers->get('referer') ?? '/';
            if ($selectedUser && !$currentUser->getContacts()->contains($selectedUser)) {
                $this->addFlash('error', 'Vous ne pouvez envoyer des messages qu\'à vos propres contacts.');

                return $this->redirect($r);
            }

            if ($selectedUser) {
                if ($request->isMethod('POST')) {
                    if (!$this->isCsrfTokenValid('message_send', $request->request->get('_token'))) {
                        $this->addFlash('danger', 'Token CSRF invalide');

                        return $this->redirectToRoute('app_message_conversation', ['id' => $id]);
                    }
                    $content = $request->request->get('content');
                    $file = $request->files->get('file_upload');

                    if (!empty($content) || $file) {
                        $message = new Message();
                        $message->setContent($content);
                        $message->setSender($currentUser);
                        $message->setRecipient($selectedUser);

                        if ($file) {
                            $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads';
                            $fileName = md5(uniqid()).'.'.$file->guessExtension();

                            try {
                                $file->move($uploadDir, $fileName);
                                $message->setAttachment($fileName);
                            } catch (\Exception $e) {
                            }
                        }

                        $entityManager->persist($message);
                        $entityManager->flush();

                        return $this->redirectToRoute('app_message_conversation', ['id' => $selectedUser->getId()]);
                    }
                }
                $messages = $messageRepository->findConversation($currentUser, $selectedUser);
            }
        }

        $reservationStatuses = [];
        $reservationRepository = $entityManager->getRepository(Reservation::class); // Assure-toi d'importer la classe Reservation !

        foreach ($messages as $message) {
            if (false !== strpos($message->getContent(), '[RES_ID:')) {
                // On extrait l'ID (ex: "[RES_ID:42] Bonjour...")
                preg_match('/\[RES_ID:(\d+)\]/', $message->getContent(), $matches);
                if (isset($matches[1])) {
                    $resId = (int) $matches[1];
                    $reservation = $reservationRepository->find($resId);
                    if ($reservation) {
                        $reservationStatuses[$resId] = $reservation->getStatut();
                    }
                }
            }
        }

        return $this->render('message/index.html.twig', [
            'reservationStatuses' => $reservationStatuses,
            'users' => $users,
            'selectedUser' => $selectedUser,
            'messages' => $messages,
            'searchTerm' => $searchTerm,
        ]);
    }
}
