<?php

namespace App\Repository;

use App\Entity\Burger;
use App\Entity\Oignon;
use App\Entity\Pain;
use App\Entity\Sauce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Burger>
 */
class BurgerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Burger::class);
    }

    /**
     * Exercice 1: Trouve les burgers contenant un ingrédient spécifique par son nom.
     */
    public function findBurgersWithIngredient(string $ingredientName): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.pain', 'p')
            ->leftJoin('b.oignons', 'o')
            ->leftJoin('b.sauces', 's')
            ->where('p.name LIKE :ingredientName')
            ->orWhere('o.name LIKE :ingredientName')
            ->orWhere('s.name LIKE :ingredientName')
            ->setParameter('ingredientName', '%' . $ingredientName . '%')
            ->distinct()
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Exercice 2: Trouve les X burgers les plus chers.
     * Version simple qui récupère tous les burgers et les trie en PHP
     */
    public function findTopXBurgers(int $limit): array
    {
        $allBurgers = $this->createQueryBuilder('b')
            ->getQuery()
            ->getResult();
        
        // Trier les burgers par prix décroissant en PHP
        usort($allBurgers, function($a, $b) {
            return floatval($b->getPrice()) <=> floatval($a->getPrice());
        });
        
        // Retourner seulement les X premiers
        return array_slice($allBurgers, 0, $limit);
    }

    /**
     * Exercice 3: Trouve les burgers ne contenant PAS un ingrédient spécifique.
     */
    public function findBurgersWithoutIngredient(object $ingredient): array
    {
        $qb = $this->createQueryBuilder('b');

        if ($ingredient instanceof Sauce) {
            return $qb->where('b.id NOT IN (
                    SELECT DISTINCT b2.id 
                    FROM App\Entity\Burger b2 
                    JOIN b2.sauces s 
                    WHERE s.id = :ingredientId
                )')
                ->setParameter('ingredientId', $ingredient->getId())
                ->orderBy('b.name', 'ASC')
                ->getQuery()
                ->getResult();
        } elseif ($ingredient instanceof Oignon) {
            return $qb->where('b.id NOT IN (
                    SELECT DISTINCT b3.id 
                    FROM App\Entity\Burger b3 
                    JOIN b3.oignons o 
                    WHERE o.id = :ingredientId
                )')
                ->setParameter('ingredientId', $ingredient->getId())
                ->orderBy('b.name', 'ASC')
                ->getQuery()
                ->getResult();
        }

        return [];
    }

    /**
     * Exercice 4: Trouve les burgers avec un nombre minimum d'ingrédients (oignons + sauces).
     */
    public function findBurgersWithMinimumIngredients(int $minIngredients): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.oignons', 'o')
            ->leftJoin('b.sauces', 's')
            ->groupBy('b.id')
            ->having('COUNT(DISTINCT o.id) + COUNT(DISTINCT s.id) >= :minIngredients')
            ->setParameter('minIngredients', $minIngredients)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Méthode pour obtenir tous les noms d'ingrédients pour l'autocomplétion
     */
    public function getAllIngredientNames(): array
    {
        $em = $this->getEntityManager();
        
        // Récupérer tous les noms de pain
        $pains = $em->getRepository(Pain::class)
            ->createQueryBuilder('p')
            ->select('p.name')
            ->getQuery()
            ->getArrayResult();
            
        // Récupérer tous les noms d'oignons
        $oignons = $em->getRepository(Oignon::class)
            ->createQueryBuilder('o')
            ->select('o.name')
            ->getQuery()
            ->getArrayResult();
            
        // Récupérer tous les noms de sauces
        $sauces = $em->getRepository(Sauce::class)
            ->createQueryBuilder('s')
            ->select('s.name')
            ->getQuery()
            ->getArrayResult();
        
        // Fusionner tous les noms
        $allNames = [];
        foreach (array_merge($pains, $oignons, $sauces) as $item) {
            $allNames[] = $item['name'];
        }
        
        return array_unique($allNames);
    }
}
