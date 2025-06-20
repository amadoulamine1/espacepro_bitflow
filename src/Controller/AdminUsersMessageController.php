<?php

namespace App\Controller;

use App\Entity\Sfd;
use App\Entity\Users;
use App\Entity\Message;
use Pagerfanta\Pagerfanta;
use App\Entity\PieceJointe;
use App\Entity\UsersMessage;
use Psr\Log\LoggerInterface;
use App\Form\AdminUsersMessageForm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use App\Repository\UsersMessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('admin/users/message')]
final class AdminUsersMessageController extends AbstractController
{
    #[Route(name: 'app_admin_users_message_index', methods: ['GET'])]
    public function index(UsersMessageRepository $repo, Request $request): Response
    {
        $limit = $request->query->getInt('limit', 10);
        $page = $request->query->getInt('page', 1);
        $sort = $request->query->get('sort', 'u.id');
        $direction = $request->query->get('direction', 'asc');
        $filterCorps = $request->query->get('filter_message_corps');

         // Adjust sort field if it refers to the message entity
         $actualSortField = $sort;
         if (str_starts_with($sort, 'u.message.')) {
             $actualSortField = 'm.' . substr($sort, strlen('u.message.'));
         }
        $qb = $repo->createQueryBuilder('u')
                    ->leftJoin('u.message', 'm')// Add join to message entity with alias 'm'
                    ->orderBy($actualSortField, $direction);
        if ($filterCorps) {
            $qb->andWhere('LOWER(m.corps)  LIKE LOWER(:corps)')->setParameter('corps', '%'.$filterCorps.'%');
        }
        
        $adapter = new QueryAdapter($qb);
        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage($adapter, $page, $limit);

        return $this->render('admin/users_message/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    #[Route('/new', name: 'app_admin_users_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger,ManagerRegistry $doctrine): Response
    {
        $usersMessage = new UsersMessage();
        $usersRepository=$doctrine->getRepository(Users::class);
        $form = $this->createForm(AdminUsersMessageForm::class, $usersMessage);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
          /*  $em->persist($message);
            $em->flush();

            if ($request->headers->get('Turbo-Frame') === 'users-message-details') {
                return $this->redirectToRoute('app_admin_users_message_show', ['id' => $message->getId()], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_admin_users_message_index');*/
            //Upload les pieces jointes et la lettre de transmission
            $this->upload($usersMessage->getMessage(),$slugger);
            $choixProfil = $form->get('choixProfil')->getData();
            $filterMode = $form->get('filterMode')->getData();
            $sfdforms = $form->get('sfds')->getData();
            $reseau= $form->get('reseau')->getData();
           
            //le guichet
            $guichet=$usersRepository->findGuichet();
            //dump( $usersMessage,$piecesJointes,$filterMode,$choixProfil,$sfdforms,$reseau,$usersMessage);

            /* Gestion SFD des destinataires */
            $entityManager = $doctrine->getManager();
            $receipters=null;
          //  dump( "sfdForm",$sfdforms,"sfds", $form->get('sfds'));
            //Si filtre par Sfd 
            /** Recuperer les sfd listés et en faire les destinataires 
            *Si Tout le monde, envoyer a tous les IMF
            *Si SFD envoyer aux sfd listes
            *SI RESEAU, envoyer aux SFd appartennant a ce reseau
            */
         //   dump( $filterMode,$choixProfil,$sfdforms,$reseau,$usersMessage);

            $sfds=null;
            if ($filterMode=="all"){
                $sfds=$doctrine->getRepository(Sfd::class)->findAllActive();
            }else  if ($filterMode=="sfds"){
                $sfds=$sfdforms;
            }else if ($filterMode=="reseau"){
                $sfds=$doctrine->getRepository(Sfd::class)->findByReseaux($reseau);
            }
            dump("sfds",$sfds);
            //Faire les envoies des messages selon les profils selectionnées

            /**
             * Si Tout le monde ,envoyer à tous les profils
             */
             dump("sfdsssss",$sfds);
             /**
              * Parcourir d'abord selon l'institution de micro finance
              * puis
              * selon le choix du destinataire "Tout le monde, Gerant, PCA, PCS, PCC"
              * si la condition est respectée
              */
             foreach($sfds as $sfd) {
                //Rechercher PCA
                $this->findUserByProfile($usersMessage, $sfd, "PCA", $doctrine, $guichet);
                //Rechercher PCS
                $this->findUserByProfile($usersMessage, $sfd, "PCS", $doctrine, $guichet);
                //Rechercher PCC
                $this->findUserByProfile($usersMessage, $sfd, "PCC", $doctrine, $guichet);
                //Rechercher Gerant
                $this->findUserByProfile($usersMessage, $sfd, "Gerant", $doctrine, $guichet);
                //dd($choixProfil);
                $entityManager->flush();
               
            }
            if ($request->headers->get('Turbo-Frame') === 'users-message-details') {
                    
                return $this->render('admin/users_message/_success.html.twig');
            }
        }

        if ($request->headers->get('Turbo-Frame') === 'users-message-details') {
            return $this->render('admin/users_message/_new.html.twig', [
                'usersMessage' => $usersMessage,
                'form' => $form->createView(),
                'action' => 'append',
            ]);
           // return $this->render('admin/users_message/_success.html.twig');
        }

        return $this->render('admin/users_message/_new.html.twig', [
            'usersMessage' => $usersMessage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_users_message_show', methods: ['GET'])]
    public function show(UsersMessage $usersMessage): Response
    {
        return $this->render('admin/users_message/_show.html.twig', [
            'usersMessage' => $usersMessage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_users_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UsersMessage $usersMessage, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdminUsersMessageForm::class, $usersMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            if ($request->headers->get('Turbo-Frame') === 'users-message-details') {
                $rowUpdateStream = $this->renderView('admin/users_message/_table_row.stream.html.twig', [
                    'usersMessage' => $usersMessage,
                    'action' => 'replace',
                ]);
                $closeModalStream = '<turbo-stream action="append" target="modal-portal"><template><script>document.dispatchEvent(new CustomEvent("modal:close"));</script></template></turbo-stream>';
                $response = new Response($rowUpdateStream . $closeModalStream);
                $response->headers->set('Content-Type', 'text/vnd.turbo-stream.html');
                return $response;
            }

            return $this->redirectToRoute('app_admin_users_message_index');
        }

        return $this->render('admin/users_message/_edit.html.twig', [
            'usersMessage' => $usersMessage,
            'form' => $form,
        ]);
    }

    #[Route('/autocomplete', name: 'app_admin_users_message_autocomplete', methods: ['GET'])]
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

  /*  #[Route('/{id}/downloadAll', name: 'downloadAll')]
    public function downloadAll(Request $request, UsersMessage $usersMessage, LoggerInterface $logger)
    {
        // dump($usersMessage);
        $filenames = $usersMessage->getMessage()->getPieceJointes();

        $opt = new \ZipStream\Option\Archive();
        //$opt->setZeroHeader(true);
        $opt->setEnableZip64(false);
        $opt->setSendHttpHeaders(true);
        $opt->setContentDisposition('attachment');
        // $opt->setContentType('application/octet-stream');
        $opt->setContentType('application/x-zip');
        //$opt->setEnableZip64(false);
        $opt->setZeroHeader(false);
        $opt->setHttpHeaderCallback('header');
        //$opt->setEnableZip64(false);

        $zip = new ZipStream($usersMessage->getMessage()->getTitre() . '.zip', $opt);

        foreach ($filenames as $f) {
            $zip->addFileFromPath($f->getOriginalFileName(), $this->getParameter('upload_destination') . "/" . $f->getPath());
        }
        $zip->finish();
        return new StreamedResponse();
    }*/

    public function findUserByProfile(UsersMessage $usersMessage,Sfd $sfd,String $fonction,ManagerRegistry $doctrine,Users $guichet){
        dump($usersMessage,$usersMessage->getChoixProfil());
        if (in_array("Tout le monde", $usersMessage->getChoixProfil()) || in_array($fonction, $usersMessage->getChoixProfil())) {
           
            $receipter=$doctrine->getRepository(Users::class)->findByProfile($sfd,$fonction);
            $entityManager = $doctrine->getManager();
            if($receipter){
                $um = clone $usersMessage;
                $um->setSender($guichet);
                $um->setRecipient($receipter);
                //  dump($um);
                if (null !==($um->getMessage()->getLettreTransmission()))
                    if(null ==($um->getMessage()->getLettreTransmission()->getFile()))
                        $um->getMessage()->setLettreTransmissionToNull();
                //  $um->getMessage()->getLettreTransmission()->setMessage($um->getMessage()) ;
                $entityManager->persist($um);
                $entityManager->flush();
                // dump($um);
            }
        }
   }
    public function uploadOneFile(Message $message,PieceJointe $pieceJointe,SluggerInterface $slugger){
    
       // dd($lettreTransmissionFile,"coucoucuo");
        if ($pieceJointe) {
            $pieceJointeFile=$pieceJointe->getFile();
            //  dump($pieceJointeFile);
            $originalFilename = pathinfo($pieceJointeFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathinfo($pieceJointeFile->getClientOriginalName(), PATHINFO_EXTENSION);
            //    dump($originalFilename);
            // this is needed to safely include the file name as part of the URL
            
            $safeFilename = $slugger->slug($originalFilename);
            //  $newFilename = $safeFilename.'-'.uniqid().'.'.$pieceJointeFile->guessExtension();
            $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;
            // dump($safeFilename,$newFilename, $extension,$this,$pieceJointeFile);
            // die();
            //Renseigner le path et l'attribut du message
            $pieceJointe->setPath($newFilename);
            $pieceJointe->setType($extension);
            $pieceJointe->setLibelle($originalFilename);
            $pieceJointe->setOriginalFileName($originalFilename.".".$extension);
            $pieceJointe->setYear((int) date('Y'));
            $pieceJointe->setMonth((int) date('m'));

            /** To do */
            // A debloquer quand la securite sera installée
           // $user = $this->security->getUser();
            $prefix="guichet";
           /* if($user){
                $sfd=$user->getSfd();
                if($sfd)
                    $prefix=$sfd->getNumAgrement(); 
            }*/
            $pieceJointe->setPrefix($prefix);
            $pieceJointe->setMessage($message);
            dump($pieceJointe);
            return $pieceJointe;
        }
    }


    public function upload(Message $message, SluggerInterface $slugger): void
    {
        //Upload la lettre de transmission
        $lettreTransmission = $message->getLettreTransmission();
        if ($lettreTransmission && $lettreTransmission->getFile() !== null) {
            $lettreTransmission->setLibelle("Lettre de Transmission");
            // Le type est généralement l'extension, qui est définie dans uploadOneFile
            $this->uploadOneFile($message, $lettreTransmission, $slugger); // Modifie $lettreTransmission par référence
        } elseif ($lettreTransmission && $lettreTransmission->getFile() === null) {
            // Si l'objet LettreTransmission existe mais n'a pas de fichier,
            // cela signifie qu'un champ de formulaire vide a été soumis. Dissociez-le.
            $message->setLettreTransmission(null);
        }

        //Upload des restes des pieces jointes
        // L'écouteur de formulaire dans MessageUsersMessageType devrait déjà avoir supprimé
        // les nouvelles PieceJointe vides de la collection.
        // Cette boucle traite celles qui restent (soit existantes, soit nouvelles avec un fichier).
        foreach ($message->getPieceJointes() as $pieceJointe) {
            // Double-vérification au cas où l'écouteur de formulaire n'aurait pas tout intercepté
            // ou pour les pièces jointes existantes où le fichier pourrait être supprimé.
            if ($pieceJointe->getFile() !== null) {
                $this->uploadOneFile($message, $pieceJointe, $slugger); // Modifie $pieceJointe par référence
            } else {
                // Si une PieceJointe sans fichier est toujours dans la collection ici
                // (par exemple, une PJ existante où le fichier a été supprimé via le formulaire mais l'entité est restée),
                // elle doit être retirée pour éviter les erreurs de persistance si son 'path' devient invalide.
                // Cependant, VichUploaderBundle gère généralement la suppression.
                // L'écouteur de formulaire est le principal responsable du nettoyage des *nouvelles* entrées vides.
            }
        }
    }

    /**
     * ex upload
     * 
     * 
     * public function upload(Message $message, SluggerInterface $slugger){
     * $pjs=new ArrayCollection();
     * *  
     *   //Upload la lettre de transmission
     *   
     *   //recuperer la piece jointe lettre de transmission dans message 
     *   $lettreTransmissionFile = $message->getLettreTransmission();

        //Verifier si la piece jointe est nulle ou pas remarque la piece jointe ne doit pas etre nullle, si la piece jointe est nulle ,l'objet ne sera pas inclus
        if( ($lettreTransmissionFile->getFile())!=null){
            //renseigner le libelle et le type comme cela a ete disable dans le formulaire pour la lettre de transmission
            $lettreTransmissionFile->setLibelle("Lettre de Transmission");
            $lettreTransmissionFile->setType("Lettre de Transmission");
            $lt=$this->uploadOneFile($message,$lettreTransmissionFile,$slugger);
            
            //l'ajouter dans la liste de piece jointe a retourner et sauvegarder
            $pjs->add($lt);
        }
        // dd($lettreTransmissionFile ,"318");
        //Upload des restes des pieces jointes
        
        $pieceJointeFiles = $message->getPieceJointes();
        //dd($pieceJointeFiles);
        //recuperer la liste des piece jointe et comme d'habitude verifier si le fichier a etet jointe
        foreach($pieceJointeFiles as $pieceJointe) {
            if( ($pieceJointe->getFile())!=null){
                $pj=$this->uploadOneFile($message,$pieceJointe,$slugger);
                $pjs->add($pj);
            }
        }
        //retourner la liste des pieces jointes a attacher a l'objet pour la sauvegarde
        return $pjs;
    }
     * 
     */
}