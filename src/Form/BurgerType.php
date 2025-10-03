<?php

namespace App\Form;

use App\Entity\Burger;
use App\Entity\Oignon;
use App\Entity\Pain;
use App\Entity\Sauce;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BurgerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => '🏷️ Nom du burger',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => '📝 Description',
                'attr' => [
                    'placeholder' => 'Décrivez votre burger...',
                    'rows' => 3,
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('price', NumberType::class, [
                'label' => '💰 Prix €',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'form-control',
                    'step' => '0.01',
                    'min' => '0'
                ],
                'required' => true,
                'scale' => 2
            ])
            ->add('pain', EntityType::class, [
                'class' => Pain::class,
                'choice_label' => 'name',
                'label' => '🍞 Pain',
                'placeholder' => '-- Choisir un pain --',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true
            ])
            ->add('oignons', EntityType::class, [
                'class' => Oignon::class,
                'choice_label' => 'name',
                'label' => '🧅 Oignons (optionnel)',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'checkbox-grid'
                ]
            ])
            ->add('sauces', EntityType::class, [
                'class' => Sauce::class,
                'choice_label' => 'name',
                'label' => '🥄 Sauces (optionnel)',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'checkbox-grid'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => '✨ Créer le burger',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Burger::class,
        ]);
    }
}
