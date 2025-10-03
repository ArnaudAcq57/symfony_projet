<?php

namespace App\Controller;

use App\Entity\Oignon;
use App\Form\OignonType;
use App\Repository\OignonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OignonController extends AbstractController
{
    #[Route('/oignons', name: 'oignon_index')]
    public function index(OignonRepository $oignonRepository): Response
    {
        $oignons = $oignonRepository->findBy([], ['name' => 'ASC']);

        return $this->render('oignon/index.html.twig', [
            'oignons' => $oignons,
        ]);
    }

    #[Route('/oignon/new', name: 'oignon_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $oignon = new Oignon();
        $form = $this->createForm(OignonType::class, $oignon);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($oignon);
            $entityManager->flush();

            $this->addFlash('success', 'L\'oignon "' . $oignon->getName() . '" a été créé avec succès !');
            return $this->redirectToRoute('oignon_index');
        }

        return $this->render('oignon/new.html.twig', [
            'oignonForm' => $form->createView(),
        ]);
    }

    #[Route('/oignon/{id}/delete', name: 'oignon_delete', methods: ['POST'])]
    public function delete(Request $request, Oignon $oignon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oignon->getId(), $request->request->get('_token'))) {
            $entityManager->remove($oignon);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'oignon a été supprimé avec succès !');
        }

        return $this->redirectToRoute('oignon_index');
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
