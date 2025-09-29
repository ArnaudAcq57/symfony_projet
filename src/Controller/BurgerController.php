<?php

namespace App\Controller;

use App\Entity\Burger;
use App\Entity\Oignon;
use App\Entity\Pain;
use App\Entity\Sauce;
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
        // Si c'est une soumission de formulaire
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description'); // Récupérer la description
            $price = $request->request->get('price'); // Garder comme string au lieu de convertir en float
            $painId = (int) $request->request->get('pain_id');
            $oignonIds = $request->request->all('oignons') ?: []; // Récupérer les oignons sélectionnés
            $sauceIds = $request->request->all('sauces') ?: []; // Récupérer les sauces sélectionnées
            
            // Récupérer le pain
            $pain = $entityManager->getRepository(Pain::class)->find($painId);
            if (!$pain) {
                throw $this->createNotFoundException('Pain non trouvé');
            }
            
            // Créer le burger
            $burger = new Burger();
            $burger->setName($name);
            $burger->setDescription($description); // Définir la description
            $burger->setPrice($price); // Passer directement la string
            $burger->setPain($pain);

            // Ajouter les oignons sélectionnés
            foreach ($oignonIds as $oignonId) {
                // Validation supplémentaire pour s'assurer que c'est un ID valide
                if (is_numeric($oignonId)) {
                    $oignon = $entityManager->getRepository(Oignon::class)->find((int)$oignonId);
                    if ($oignon) {
                        $burger->addOignon($oignon);
                    }
                }
            }

            // Ajouter les sauces sélectionnées
            foreach ($sauceIds as $sauceId) {
                // Validation supplémentaire pour s'assurer que c'est un ID valide
                if (is_numeric($sauceId)) {
                    $sauce = $entityManager->getRepository(Sauce::class)->find((int)$sauceId);
                    if ($sauce) {
                        $burger->addSauce($sauce);
                    }
                }
            }

            $entityManager->persist($burger);
            $entityManager->flush();

            return $this->redirectToRoute('app_burger_list');
        }

        // Afficher le formulaire de création
        $pains = $entityManager->getRepository(Pain::class)->findAll();
        $oignons = $entityManager->getRepository(Oignon::class)->findAll();
        $sauces = $entityManager->getRepository(Sauce::class)->findAll();

        return $this->render('burger/burger_create.html.twig', [
            'pains' => $pains,
            'oignons' => $oignons,
            'sauces' => $sauces,
        ]);
    }

    #[Route('/burger/{id}/delete', name: 'app_burger_delete', methods: ['POST'])]
    public function delete(Request $request, Burger $burger, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$burger->getId(), $request->request->get('_token'))) {
            $entityManager->remove($burger);
            $entityManager->flush();
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
