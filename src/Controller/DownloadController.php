<?php

namespace App\Controller;

use ZipArchive;
use App\Entity\Message;
use App\Entity\PieceJointe;
use App\Entity\UsersMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Gestion des downloads des pieces jointes 
 */
class DownloadController extends AbstractController
{
 /*   #[Route('/download', name: 'app_download')]
    public function index(): Response
    {
        return $this->render('download/index.html.twig', [
            'controller_name' => 'DownloadController',
        ]);
    }*/

    #[Route('/download/{id}', name: 'piece_jointe_download')]
    public function download(PieceJointe $pieceJointe, DownloadHandler $downloadHandler): Response
    {
        return $downloadHandler->downloadObject($pieceJointe, 'file', null, $pieceJointe->getOriginalFileName());
    }

    #[Route('/download-all/{id}/{umId}', name: 'pieces_jointes_download_all')]
    public function downloadAll(Message $message,UsersMessage $usersMessage): Response
    {
        $zip = new ZipArchive();
        $nom="";
       // dump($usersMessage->getSender()->getNom());
        if($usersMessage->getSender()->getNom()=="guichet" || $usersMessage->getSender()->getSfd()==null){
            $nom = $usersMessage->getRecipient()->getSfd()->getSigle();
        } else{
            $nom = $usersMessage->getSender()->getSfd()->getSigle();
        }
        $nom = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nom);
        $zipFileName = sprintf($nom.'_%d.zip', $message->getId());
        $zipFilePath = $this->getParameter('kernel.project_dir') . '/var/' . $zipFileName;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Impossible de créer l'archive ZIP.");
        }

        // Ajouter chaque fichier à l'archive ZIP
        foreach ($message->getPieceJointes() as $pieceJointe) {
            $filePath = $this->getParameter('kernel.project_dir') . '/public/piece_jointes/' . $pieceJointe->getPrefix().'/' .$pieceJointe->getYear().'/'. $pieceJointe->getMonth().'/'.$pieceJointe->getPath();
            dump($filePath, $pieceJointe);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $pieceJointe->getOriginalFileName());
            }
        }

        $zip->close();

        // Stream le fichier ZIP au client
        return new StreamedResponse(function () use ($zipFilePath) {
            readfile($zipFilePath);
            unlink($zipFilePath); // Supprime le fichier après téléchargement
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"'
        ]);
    }

}
