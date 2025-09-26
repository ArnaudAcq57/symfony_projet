<?php

namespace App\Controller;

use App\Entity\Oignon;
use App\Repository\OignonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OignonController extends AbstractController
{
    #[Route('/oignon', name: 'oignon_index')]
    public function index(OignonRepository $oignonRepository): Response
    {
        $oignons = $oignonRepository->findAll();

        return $this->render('oignon/index.html.twig', [
            'oignons' => $oignons,
        ]);
    }

    #[Route('/oignon/create', name: 'oignon_create')]
    public function create(EntityManagerInterface $entityManager): Response
    {
        $oignon = new Oignon();
        $oignon->setName('Oignon caramélisé');

        $entityManager->persist($oignon);
        $entityManager->flush();

        return new Response('Oignon créé avec succès !');
    }
}
