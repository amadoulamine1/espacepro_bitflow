<?php

namespace App\Controller;

use App\Entity\UsersMessage;
use App\Form\UsersMessageForm;
use App\Repository\UsersMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/users/message')]
final class UsersMessageController extends AbstractController
{
    #[Route(name: 'app_users_message_index', methods: ['GET'])]
    public function index(UsersMessageRepository $repo, Request $request): Response
    {
        $limit = $request->query->getInt('limit', 10);
        $page = $request->query->getInt('page', 1);
        $sort = $request->query->get('sort', 'u.id');
        $direction = $request->query->get('direction', 'asc');
        $filterContent = $request->query->get('filter_content');

        $qb = $repo->createQueryBuilder('u')->orderBy($sort, $direction);
        if ($filterContent) {
            $qb->andWhere('u.content LIKE :content')->setParameter('content', '%'.$filterContent.'%');
        }

        $adapter = new QueryAdapter($qb);
        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage($adapter, $page, $limit);

        return $this->render('users_message/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    #[Route('/new', name: 'app_users_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $message = new UsersMessage();
        $form = $this->createForm(UsersMessageForm::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($message);
            $em->flush();

            if ($request->headers->get('Turbo-Frame') === 'users-message-details') {
                return $this->redirectToRoute('app_users_message_show', ['id' => $message->getId()], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_users_message_index');
        }

        if ($request->headers->get('Turbo-Frame') === 'users-message-details') {
            return $this->render('users_message/_detail_template.html.twig', [
                'usersMessage' => $message,
                'form' => $form->createView(),
                'action' => 'append',
            ]);
        }

        return $this->render('users_message/_new.html.twig', [
            'usersMessage' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_users_message_show', methods: ['GET'])]
    public function show(UsersMessage $usersMessage): Response
    {
        return $this->render('users_message/_show.html.twig', [
            'usersMessage' => $usersMessage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_users_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UsersMessage $usersMessage, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UsersMessageForm::class, $usersMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            if ($request->headers->get('Turbo-Frame') === 'users-message-details') {
                $rowUpdateStream = $this->renderView('users_message/_table_row.stream.html.twig', [
                    'usersMessage' => $usersMessage,
                    'action' => 'replace',
                ]);
                $closeModalStream = '<turbo-stream action="append" target="modal-portal"><template><script>document.dispatchEvent(new CustomEvent("modal:close"));</script></template></turbo-stream>';
                $response = new Response($rowUpdateStream . $closeModalStream);
                $response->headers->set('Content-Type', 'text/vnd.turbo-stream.html');
                return $response;
            }

            return $this->redirectToRoute('app_users_message_index');
        }

        return $this->render('users_message/_edit.html.twig', [
            'usersMessage' => $usersMessage,
            'form' => $form,
        ]);
    }

    #[Route('/autocomplete', name: 'app_users_message_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, UsersMessageRepository $usersMessageRepository): JsonResponse
    {
        $query = $request->query->get('query');
        $field = $request->query->get('field'); // e.g., 'sigle', 'nomDeveloppe', 'email'

        if (!$query || !$field) {
            return $this->json([]);
        }

        $suggestions = $usersMessageRepository->findSuggestions($field, $query);

        return $this->json($suggestions);
    }
}