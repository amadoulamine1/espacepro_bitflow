<?php

namespace App\Controller;

use App\Entity\UsersMessage;
use App\Form\UsersMessageForm;
use App\Repository\UsersMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users/message')]
final class UsersMessageController extends AbstractController
{
    #[Route(name: 'app_users_message_index', methods: ['GET'])]
    public function index(UsersMessageRepository $usersMessageRepository): Response
    {
        return $this->render('users_message/index.html.twig', [
            'users_messages' => $usersMessageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_users_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $usersMessage = new UsersMessage();
        $form = $this->createForm(UsersMessageForm::class, $usersMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($usersMessage);
            $entityManager->flush();

            return $this->redirectToRoute('app_users_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users_message/new.html.twig', [
            'users_message' => $usersMessage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_users_message_show', methods: ['GET'])]
    public function show(UsersMessage $usersMessage): Response
    {
        return $this->render('users_message/show.html.twig', [
            'users_message' => $usersMessage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_users_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UsersMessage $usersMessage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsersMessageForm::class, $usersMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_users_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users_message/edit.html.twig', [
            'users_message' => $usersMessage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_users_message_delete', methods: ['POST'])]
    public function delete(Request $request, UsersMessage $usersMessage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usersMessage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($usersMessage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_users_message_index', [], Response::HTTP_SEE_OTHER);
    }
}
