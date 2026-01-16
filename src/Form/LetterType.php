<?php

namespace App\Form;

use App\Entity\Letter;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LetterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la lettre',
                'attr' => ['placeholder' => 'Ex : Pour mon moi du futur...']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Ton message',
                'attr' => ['rows' => 10, 'placeholder' => 'Écris tout ce que tu as sur le cœur...']
            ])
            ->add('sendDate', null, [
                'widget' => 'single_text',
                'label' => 'Date d\'ouverture souhaitée',
                'attr' => [
                    'min' => (new \DateTime('+1 day'))->format('Y-m-d')
                ]
            ])

            ->add('product', EntityType::class, [
                'class' => Product::class,
                'label' => 'Ajouter un cadeau (Optionnel)',
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'Aucun cadeau',
                'choice_attr' => function ($product) {
                    return [
                        'data-price' => $product->getPrice(),
                        'data-image' => $product->getImage() ?? '',
                    ];
                },
            ])

            ->add('deliveryAddress', TextareaType::class, [
                'label' => 'Adresse complète',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Ex : 12 Rue des Fleurs, Quartier Racine...'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => ['placeholder' => 'Ex : Casablanca']
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
                'attr' => ['placeholder' => 'Ex : 0773656406']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Letter::class,
        ]);
    }
}
