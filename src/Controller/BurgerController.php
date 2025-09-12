<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BurgerController extends AbstractController
{
    // Simule une base de données de burgers
    private const BURGERS = [
        ['id' => 1, 'name' => 'Le Classique', 'description' => 'Pain, steak, salade, tomate, oignons.'],
        ['id' => 2, 'name' => 'Le Baconator', 'description' => 'Pain, double steak, double bacon, fromage.'],
        ['id' => 3, 'name' => 'Le Végé Deluxe', 'description' => 'Pain, galette de légumes, salade, avocat.'],
        ['id' => 4, 'name' => 'Le Makogon', 'description' => 'Pain brioché, steak de 180g, cheddar maturé, sauce secrète.'],
    ];

    #[Route('/burgers', name: 'app_burger_list')]
    public function list(): Response
    {
        return $this->render('burger/burgers_list.html.twig', [
            'burgers' => self::BURGERS,
        ]);
    }

    #[Route('/burger/{id}', name: 'app_burger_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $burger = null;
        foreach (self::BURGERS as $b) {
            if ($b['id'] === $id) {
                $burger = $b;
                break;
            }
        }

        if (!$burger) {
            throw $this->createNotFoundException('Le burger demandé n\'existe pas.');
        }

        return $this->render('burger/burger_show.html.twig', [
            'burger' => $burger,
        ]);
    }
}
