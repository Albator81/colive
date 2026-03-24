<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Csrf\CsrfTokenManagerInterface;

class MessageController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/message', name: 'app_message')]
    #[Route('/message/{id}', name: 'app_message_conversation')]
    public function index(?int $id, MessageRepository $messageRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('message_send', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }
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

            if ($selectedUser && !$currentUser->getContacts()->contains($selectedUser)) {
                throw $this->createAccessDeniedException('You can only send messages to your owns contacts.');
            }

            if ($selectedUser) {
                if ($request->isMethod('POST')) {
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

        return $this->render('message/index.html.twig', [
            'users' => $users,
            'selectedUser' => $selectedUser,
            'messages' => $messages,
            'searchTerm' => $searchTerm,
        ]);
    }
}
