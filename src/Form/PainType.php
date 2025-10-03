<?php

namespace App\Form;

use App\Entity\Pain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => '🍞 Nom du pain',
                'attr' => [
                    'placeholder' => 'Ex: Pain brioche',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => '✨ Créer le pain',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pain::class,
        ]);
    }
}
