<?php

namespace App\Controller;

use App\Entity\Pain;
use App\Form\PainType;
use App\Repository\PainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PainController extends AbstractController
{
    #[Route('/pains', name: 'pain_index')]
    public function index(PainRepository $painRepository): Response
    {
        $pains = $painRepository->findBy([], ['name' => 'ASC']);

        return $this->render('pain/index.html.twig', [
            'pains' => $pains,
        ]);
    }

    #[Route('/pain/new', name: 'pain_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pain = new Pain();
        $form = $this->createForm(PainType::class, $pain);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pain);
            $entityManager->flush();

            $this->addFlash('success', 'Le pain "' . $pain->getName() . '" a été créé avec succès !');
            return $this->redirectToRoute('pain_index');
        }

        return $this->render('pain/new.html.twig', [
            'painForm' => $form->createView(),
        ]);
    }

    #[Route('/pain/{id}/delete', name: 'pain_delete', methods: ['POST'])]
    public function delete(Request $request, Pain $pain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pain->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pain);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le pain a été supprimé avec succès !');
        }

        return $this->redirectToRoute('pain_index');
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
