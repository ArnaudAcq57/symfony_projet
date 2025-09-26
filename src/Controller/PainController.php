<?php

namespace App\Controller;

use App\Entity\Pain;
use App\Repository\PainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PainController extends AbstractController
{
    #[Route('/pain', name: 'pain_index')]
    public function index(PainRepository $painRepository): Response
    {
        $pains = $painRepository->findAll();

        return $this->render('pain/index.html.twig', [
            'pains' => $pains,
        ]);
    }

    #[Route('/pain/create', name: 'pain_create')]
    public function create(EntityManagerInterface $entityManager): Response
    {
        $pain = new Pain();
        $pain->setName('Pain aux graines');

        $entityManager->persist($pain);
        $entityManager->flush();

        return new Response('Pain créé avec succès !');
    }
}
