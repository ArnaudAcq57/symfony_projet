<?php

namespace App\Form;

use App\Entity\Oignon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OignonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => '🧅 Nom de l\'oignon',
                'attr' => [
                    'placeholder' => 'Ex: Oignon caramélisé',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => '✨ Créer l\'oignon',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Oignon::class,
        ]);
    }
}
