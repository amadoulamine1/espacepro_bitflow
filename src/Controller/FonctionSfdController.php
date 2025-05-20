<?php

namespace App\Controller;

use Pagerfanta\Pagerfanta;
use App\Entity\FonctionSfd;
use App\Form\FonctionSfdForm;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FonctionSfdRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/fonction/sfd')]
final class FonctionSfdController extends AbstractController
{

    
    #[Route(name: 'app_fonction_sfd_index', methods: ['GET'])]
    public function index(FonctionSfdRepository $sfdRepository, Request $request): Response
    {
        $limit = $request->query->getInt('limit', 10); // Get limit from request, default to 10
        $page = $request->query->getInt('page', 1); // Get page from request, default to 1
        
         // Paramètres de tri
        $sort = $request->query->get('sort', 's.id'); // Champ de tri par défaut
        $direction = $request->query->get('direction', 'asc'); // Direction par défaut

        // Paramètres de filtre
        $filterSigle = $request->query->get('filter_sigle');
        $filterLibelle = $request->query->get('filter_libelle');

        $queryBuilder = $sfdRepository->createQueryBuilder('s')
            ->orderBy($sort, $direction); // Ajoutez un tri par défaut si vous le souhaitez
        
        if ($search = $request->query->get('search')) {
            $queryBuilder
                ->where('s.sigle LIKE :search OR s.libelle LIKE :search ')
                ->setParameter('search', '%'.$search.'%');
        }
        if ($filterSigle) {
            $queryBuilder->andWhere('s.sigle LIKE :sigle')
                ->setParameter('sigle', '%' . $filterSigle . '%');
        }
        if ($filterLibelle) {
            $queryBuilder->andWhere('s.libelle LIKE :libelle')
                ->setParameter('libelle', '%' . $filterLibelle . '%');
        }
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $page, /*page number*/
            $limit /*limit per page*/
        );

        return $this->render('fonction_sfd/index.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    #[Route('/new', name: 'app_fonction_sfd_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fonctionSfd = new FonctionSfd();
        $form = $this->createForm(FonctionSfdForm::class, $fonctionSfd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fonctionSfd);
            $entityManager->flush();

            if ($request->headers->get('Turbo-Frame') === 'fonction-sfd-details') {
                return $this->redirectToRoute('app_fonction_sfd_show', ['id' => $fonctionSfd->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_fonction_sfd_index', [], Response::HTTP_SEE_OTHER);
        }

         // Si la requête est pour un turbo-frame, on rend le template partiel
         if ($request->headers->get('Turbo-Frame') === 'fonction-sfd-details') {
            return $this->render('fonctionsfd/_new.html.twig', [
                'sfd' => $fonctionSfd,
                'form' => $form->createView(),
            ]);
        }

       /* return $this->render('fonction_sfd/_new.html.twig', [
            'fonction_sfd' => $fonctionSfd,
            'form' => $form,
        ]);*/
        return $this->render('fonction_sfd/_table_row.stream.html.twig', [
            'fonctionSfd' => $fonctionSfd,
        ]);
    }

    #[Route('/{id}', name: 'app_fonction_sfd_show', methods: ['GET'])]
    public function show(FonctionSfd $fonctionSfd): Response
    {
        return $this->render('fonction_sfd/_show.html.twig', [
            'fonction_sfd' => $fonctionSfd,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fonction_sfd_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FonctionSfd $fonctionSfd, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FonctionSfdForm::class, $fonctionSfd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            if ($request->headers->get('Turbo-Frame') === 'fonction-sfd-details') {
                return $this->render('fonction_sfd/_table_row.stream.html.twig', [
                    'fonctionSfd' => $fonctionSfd,
                ]);
            }

            return $this->redirectToRoute('app_fonction_sfd_index', [], Response::HTTP_SEE_OTHER);
        }

       
        return $this->render('fonction_sfd/_edit.html.twig', [
            'fonction_sfd' => $fonctionSfd,
            'form' => $form,
        ]);
    }

/*    #[Route('/{id}', name: 'app_fonction_sfd_delete', methods: ['POST'])]
    public function delete(Request $request, FonctionSfd $fonctionSfd, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fonctionSfd->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fonctionSfd);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fonction_sfd_index', [], Response::HTTP_SEE_OTHER);
    }*/

    
    #[Route('/test/autocomplete', name: 'app_fonction_sfd_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, FonctionSfdRepository $sfdRepository): JsonResponse
    {
        $query = $request->query->get('query');
        $field = $request->query->get('field'); // e.g., 'sigle', 'nomDeveloppe', 'email'

        if (!$query || !$field) {
            return $this->json([]);
        }

        $suggestions = $sfdRepository->findSuggestions($field, $query);

        return $this->json($suggestions);
    }
}
