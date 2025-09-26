<?php

namespace App\DataFixtures;

use App\Entity\Burger;
use App\Entity\Commentaire;
use App\Entity\Oignon;
use App\Entity\Pain;
use App\Entity\Sauce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer les pains
        $painClassique = new Pain();
        $painClassique->setName('Pain classique');
        $manager->persist($painClassique);

        $painBrioche = new Pain();
        $painBrioche->setName('Pain brioche');
        $manager->persist($painBrioche);

        $painSesame = new Pain();
        $painSesame->setName('Pain aux graines de sésame');
        $manager->persist($painSesame);

        // Créer les sauces
        $sauceKetchup = new Sauce();
        $sauceKetchup->setName('Ketchup');
        $manager->persist($sauceKetchup);

        $sauceMayonnaise = new Sauce();
        $sauceMayonnaise->setName('Mayonnaise');
        $manager->persist($sauceMayonnaise);

        $sauceBBQ = new Sauce();
        $sauceBBQ->setName('Sauce BBQ');
        $manager->persist($sauceBBQ);

        $sauceMoutarde = new Sauce();
        $sauceMoutarde->setName('Moutarde');
        $manager->persist($sauceMoutarde);

        // Créer les oignons
        $oignonRouge = new Oignon();
        $oignonRouge->setName('Oignon rouge');
        $manager->persist($oignonRouge);

        $oignonCaramelise = new Oignon();
        $oignonCaramelise->setName('Oignon caramélisé');
        $manager->persist($oignonCaramelise);

        $oignonFrit = new Oignon();
        $oignonFrit->setName('Oignon frit');
        $manager->persist($oignonFrit);

        // Burger 1 : Le Classique
        $burgerClassique = new Burger();
        $burgerClassique->setName('Le Classique');
        $burgerClassique->setDescription('Un burger traditionnel avec des ingrédients de qualité');
        $burgerClassique->setPrice('8.50');
        $burgerClassique->setPain($painClassique);
        $burgerClassique->addSauce($sauceKetchup);
        $burgerClassique->addSauce($sauceMayonnaise);
        $burgerClassique->addOignon($oignonRouge);
        $manager->persist($burgerClassique);

        // Burger 2 : Le Gourmet
        $burgerGourmet = new Burger();
        $burgerGourmet->setName('Le Gourmet');
        $burgerGourmet->setDescription('Un burger raffiné pour les palais exigeants');
        $burgerGourmet->setPrice('12.90');
        $burgerGourmet->setPain($painBrioche);
        $burgerGourmet->addSauce($sauceMoutarde);
        $burgerGourmet->addOignon($oignonCaramelise);
        $manager->persist($burgerGourmet);

        // Burger 3 : Le BBQ King
        $burgerBBQ = new Burger();
        $burgerBBQ->setName('Le BBQ King');
        $burgerBBQ->setDescription('Un burger épicé avec une sauce barbecue maison');
        $burgerBBQ->setPrice('11.50');
        $burgerBBQ->setPain($painSesame);
        $burgerBBQ->addSauce($sauceBBQ);
        $burgerBBQ->addSauce($sauceMayonnaise);
        $burgerBBQ->addOignon($oignonFrit);
        $burgerBBQ->addOignon($oignonRouge);
        $manager->persist($burgerBBQ);

        // Créer quelques commentaires
        $commentaire1 = new Commentaire();
        $commentaire1->setName('Excellent burger, je recommande vivement !');
        $commentaire1->setBurger($burgerClassique);
        $manager->persist($commentaire1);

        $commentaire2 = new Commentaire();
        $commentaire2->setName('Un délice absolu, les saveurs sont parfaites.');
        $commentaire2->setBurger($burgerGourmet);
        $manager->persist($commentaire2);

        $commentaire3 = new Commentaire();
        $commentaire3->setName('La sauce BBQ est incroyable !');
        $commentaire3->setBurger($burgerBBQ);
        $manager->persist($commentaire3);

        $commentaire4 = new Commentaire();
        $commentaire4->setName('Rapport qualité-prix excellent.');
        $commentaire4->setBurger($burgerClassique);
        $manager->persist($commentaire4);

        $manager->flush();
    }
}
