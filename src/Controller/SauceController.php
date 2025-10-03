<?php

namespace App\Controller;

use App\Entity\Sauce;
use App\Form\SauceType;
use App\Repository\SauceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SauceController extends AbstractController
{
    #[Route('/sauces', name: 'sauce_index')]
    public function index(SauceRepository $sauceRepository): Response
    {
        $sauces = $sauceRepository->findBy([], ['name' => 'ASC']);

        return $this->render('sauce/index.html.twig', [
            'sauces' => $sauces,
        ]);
    }

    #[Route('/sauce/new', name: 'sauce_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sauce = new Sauce();
        $form = $this->createForm(SauceType::class, $sauce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sauce);
            $entityManager->flush();

            $this->addFlash('success', 'La sauce "' . $sauce->getName() . '" a été créée avec succès !');
            return $this->redirectToRoute('sauce_index');
        }

        return $this->render('sauce/new.html.twig', [
            'sauceForm' => $form->createView(),
        ]);
    }

    #[Route('/sauce/{id}/delete', name: 'sauce_delete', methods: ['POST'])]
    public function delete(Request $request, Sauce $sauce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sauce->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sauce);
            $entityManager->flush();
            
            $this->addFlash('success', 'La sauce a été supprimée avec succès !');
        }

        return $this->redirectToRoute('sauce_index');
    }

    #[Route('/sauce/create', name: 'sauce_create')]
    public function create(EntityManagerInterface $entityManager): Response
    {
        $sauce = new Sauce();
        $sauce->setName('Sauce piquante');

        $entityManager->persist($sauce);
        $entityManager->flush();

        return new Response('Sauce créée avec succès !');
    }
}
