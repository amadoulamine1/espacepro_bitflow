<?php

namespace App\Controller;

use App\Entity\Sfd;
use App\Form\AdminSfdForm;
use Pagerfanta\Pagerfanta;
use App\Repository\SfdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/sfd')]
final class AdminSfdController extends AbstractController
{
/**
 * Displays a list of all SFDs.
 *
 * @param SfdRepository $sfdRepository The repository to fetch SFD data.
 * @return Response The response object containing the rendered view.
 */

    #[Route(name: 'app_admin_sfd_index', methods: ['GET'])]
    public function index(SfdRepository $sfdRepository, Request $request): Response
    {
        $limit = $request->query->getInt('limit', 10); // Get limit from request, default to 10
        $page = $request->query->getInt('page', 1); // Get page from request, default to 1
        
         // Paramètres de tri
        $sort = $request->query->get('sort', 's.id'); // Champ de tri par défaut
        $direction = $request->query->get('direction', 'asc'); // Direction par défaut

        // Paramètres de filtre
        $filterSigle = $request->query->get('filter_sigle');
        $filterNumAgrement = $request->query->get('filter_num_agrement');
        $filterNomDeveloppe = $request->query->get('filter_nom_developpe');
        $filterEmail = $request->query->get('filter_email');
        $queryBuilder = $sfdRepository->createQueryBuilder('s')
            ->orderBy($sort, $direction); // Ajoutez un tri par défaut si vous le souhaitez
        
        if ($search = $request->query->get('search')) {
            $queryBuilder
                ->where('s.numAgrement LIKE :search OR s.sigle LIKE :search OR s.nomDeveloppe LIKE :search OR s.email LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }
        if ($filterSigle) {
            $queryBuilder->andWhere('s.sigle LIKE :sigle')
                ->setParameter('sigle', '%' . $filterSigle . '%');
        }
        if ($filterNumAgrement) {
            $queryBuilder->andWhere('s.numAgrement LIKE :numAgrement')
                ->setParameter('numAgrement', '%' . $filterNumAgrement . '%');
        }
        if ($filterNomDeveloppe) {
            $queryBuilder->andWhere('s.nomDeveloppe LIKE :nomDeveloppe')
                ->setParameter('nomDeveloppe', '%' . $filterNomDeveloppe . '%');
        }
        if ($filterEmail) {
            $queryBuilder->andWhere('s.email LIKE :email')
                ->setParameter('email', '%' . $filterEmail . '%');
        }
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $page, /*page number*/
            $limit /*limit per page*/
        );

        return $this->render('/admin/sfd/index.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    #[Route('/new', name: 'app_admin_sfd_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sfd = new Sfd();
        $form = $this->createForm(AdminSfdForm::class, $sfd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sfd);
            $entityManager->flush();
            // Si la requête vient du turbo-frame du modal
            if ($request->headers->get('Turbo-Frame') === 'sfd-details') {
                return $this->redirectToRoute('app_admin_sfd_show', ['id' => $sfd->getId()], Response::HTTP_SEE_OTHER);
            }

          //      return $this->redirectToRoute('app_admin_sfd_index', [], Response::HTTP_SEE_OTHER);
        }

        // Si la requête est pour un turbo-frame, on rend le template partiel
        if ($request->headers->get('Turbo-Frame') === 'sfd-details') {
            return $this->render('admin/sfd/_detail_template.html.twig', [
                'sfd' => $sfd,
                'form' => $form->createView(),
                'action' => 'append',
            ]);
        }

        return $this->render('admin/sfd/_new.html.twig', [
            'sfd' => $sfd,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_sfd_show', methods: ['GET'])]
    public function show(Sfd $sfd): Response
    {
        return $this->render('sfd/_show.html.twig', [
            'sfd' => $sfd,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_sfd_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sfd $sfd, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminSfdForm::class, $sfd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Après un enregistrement réussi, si la requête vient du turbo-frame du modal
            if ($request->headers->get('Turbo-Frame') === 'sfd-details') {
                 // Render the stream for updating the row
                $rowUpdateStream = $this->renderView('admin/sfd/_table_row.stream.html.twig', [
                    'sfd' => $sfd,
                    'action' => 'replace',
                ]);
                // Stream to dispatch the modal close event by appending a script
                $closeModalStream = '<turbo-stream action="append" target="modal-portal">' .
                                    '<template>' .
                                    '<script>document.dispatchEvent(new CustomEvent("modal:close"));</script>' .
                                    '</template>' .
                                    '</turbo-stream>';


                $response = new Response(implode('', [$rowUpdateStream, $closeModalStream]));
                $response->headers->set('Content-Type', 'text/vnd.turbo-stream.html');
                return $response;
            }
            // Fallback pour les soumissions de formulaire non-Turbo Frame (page complète)
            return $this->redirectToRoute('app_admin_sfd_index', [], Response::HTTP_SEE_OTHER);
        }

        // Pour les requêtes GET (affichage initial du formulaire)
        // ou les requêtes POST avec un formulaire invalide.
        // On rend toujours _edit.html.twig car c'est le contenu destiné au turbo-frame du modal.
        return $this->render('sfd/_edit.html.twig', [
            'sfd' => $sfd,
            'form' => $form->createView(),
        ]);
    }

    /*#[Route('/{id}', name: 'app_sfd_delete', methods: ['POST'])]
    public function delete(Request $request, Sfd $sfd, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sfd->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sfd);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sfd_index', [], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/autocomplete', name: 'app_admin_sfd_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, SfdRepository $sfdRepository): JsonResponse
    {
        $query = $request->query->get('query');
        $field = $request->query->get('field'); // e.g., 'sigle', 'nomDeveloppe', 'email'

        if (!$query || !$field) {
            return $this->json([]);
        }

        $suggestions = $sfdRepository->findSuggestions($field, $query);

        return $this->json($suggestions);
    }

    #[Route('/reseau/autocomplete', name: 'app_admin_sfd_reseau_autocomplete', methods: ['GET'])]
    public function reseauAutocomplete(Request $request, SfdRepository $sfdRepository): JsonResponse
    {
        $query = $request->query->get('query', '');
        if (!$query) {
            return $this->json([]);
        }

        // Recherche des réseaux distincts existants contenant la chaîne recherchée
        $qb = $sfdRepository->createQueryBuilder('s')
            ->select('DISTINCT s.reseau')
            ->where('s.reseau LIKE :q')
            ->setParameter('q', '%'.$query.'%')
            ->orderBy('s.reseau', 'ASC')
            ->setMaxResults(10);

        $reseaux = array_column($qb->getQuery()->getResult(), 'reseau');

        // Format attendu par UX Autocomplete ou ton JS
        $results = [];
        foreach ($reseaux as $reseau) {
            if ($reseau) {
                $results[] = ['value' => $reseau, 'text' => $reseau];
            }
        }

        return $this->json($results);
    }
}
