<?php

namespace App\Controller;

use App\Entity\Burger;
use App\Form\BurgerType;
use App\Repository\BurgerRepository;
use App\Repository\SauceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BurgerController extends AbstractController
{
    #[Route('/burgers', name: 'app_burger_list')]
    public function index(BurgerRepository $burgerRepository): Response
    {
        $burgers = $burgerRepository->findAll();

        return $this->render('burger/burgers_list.html.twig', [
            'burgers' => $burgers,
        ]);
    }

    #[Route('/burger/{id}', name: 'app_burger_show', requirements: ['id' => '\d+'])]
    public function show(Burger $burger): Response
    {
        return $this->render('burger/burger_show.html.twig', [
            'burger' => $burger,
        ]);
    }

    #[Route('/burger/create', name: 'burger_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $burger = new Burger();
        $form = $this->createForm(BurgerType::class, $burger);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($burger);
            $entityManager->flush();
            
            // Message flash pour confirmer la création
            $this->addFlash('success', 'Le burger "' . $burger->getName() . '" a été créé avec succès !');
            
            return $this->redirectToRoute('app_burger_list');
        }

        return $this->render('burger/burger_create.html.twig', [
            'burgerForm' => $form->createView(),
        ]);
    }

    #[Route('/burger/{id}/delete', name: 'app_burger_delete', methods: ['POST'])]
    public function delete(Request $request, Burger $burger, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$burger->getId(), $request->request->get('_token'))) {
            $entityManager->remove($burger);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le burger a été supprimé avec succès !');
        }

        return $this->redirectToRoute('app_burger_list');
    }

    #[Route('/burgers/search', name: 'app_burger_search')]
    public function search(Request $request, BurgerRepository $burgerRepository, SauceRepository $sauceRepository): Response
    {
        $searchType = $request->query->get('search_type');
        $results = [];
        $searchParams = [];

        switch ($searchType) {
            case 'with_ingredient':
                $ingredientName = $request->query->get('ingredient');
                if ($ingredientName) {
                    $results = $burgerRepository->findBurgersWithIngredient($ingredientName);
                }
                $searchParams = ['ingredientName' => $ingredientName];
                break;

            case 'top_expensive':
                $limit = $request->query->getInt('limit', 5);
                $results = $burgerRepository->findTopXBurgers($limit);
                $searchParams = ['limit' => $limit];
                break;

            case 'without_ingredient':
                $sauceId = $request->query->getInt('sauce_id');
                if ($sauceId) {
                    $sauce = $sauceRepository->find($sauceId);
                    if ($sauce) {
                        $results = $burgerRepository->findBurgersWithoutIngredient($sauce);
                        $searchParams = ['sauce' => $sauce];
                    }
                }
                break;

            case 'min_ingredients':
                $min = $request->query->getInt('min', 2);
                $results = $burgerRepository->findBurgersWithMinimumIngredients($min);
                $searchParams = ['min' => $min];
                break;
        }

        return $this->render('burger/search.html.twig', [
            'searchType' => $searchType,
            'results' => $results,
            'searchParams' => $searchParams,
            'all_sauces' => $sauceRepository->findBy([], ['name' => 'ASC']),
            'all_ingredient_names' => $burgerRepository->getAllIngredientNames(),
        ]);
    }

    #[Route('/api/ingredients', name: 'api_ingredients', methods: ['GET'])]
    public function getIngredients(BurgerRepository $burgerRepository): Response
    {
        $ingredients = $burgerRepository->getAllIngredientNames();
        return $this->json($ingredients);
    }
}
