<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentaireController extends AbstractController
{
    #[Route('/commentaire', name: 'commentaire_index')]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findAll();

        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/commentaire/create', name: 'commentaire_create')]
    public function create(EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $commentaire->setName('Délicieux, je recommande !');

        $entityManager->persist($commentaire);
        $entityManager->flush();

        return new Response('Commentaire créé avec succès !');
    }
}
