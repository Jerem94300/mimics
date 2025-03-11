<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => "Référence",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une référence"
                    ])
                ]
            ])
            ->add('title', TextType::class, [
                'label' => "Titre",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un titre"
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => "Déscription",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une description"
                    ])
                ],
                'attr' => [
                    'rows' => 10
                ]
            ])
            ->add('color', ChoiceType::class, [
                'label' => "Couleur",
                'choices' => [
                    'Blanc' => 'blanc',
                    'Noir' => 'noir',
                    'Gris sidéral' => 'gris sidéral',
                    'Bleu' => "bleu",
                    'Rose' => "rose"
                ]
            ])
            ->add('size', ChoiceType::class, [
                'label' => "Mémoire",
                'choices' => [
                    '128g' => '128g',
                    '256g' => '256g',
                    '512g' => '512g'

                ]
            ])
            ->add('gender', ChoiceType::class, [
                'label' => "Mémoire",
                'choices' => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                    'Mixte' => 'mixte'

                ]
            ])
            ->add('picture', FileType::class, [
                'label' => "Photo produit",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'imagesjpg',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => "Formats autorisés : jpg/jpeg/png/webp"
                    ])
                ]
            ])
            ->add('price', TextType::class, [
                'label' => "Prix",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un prix"
                    ])
                ]
            ])
            ->add('stock', TextType::class, [
                'label' => "Stock",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un stock"
                    ])
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            // add('category') correspond a la clé étrangère SQL category, Ici c'est un champ qui provient d'une autre table donc un champ EntityType cela va génerer dans le formulaire, une liste déroulante avec toute les catégories et les titres des catégories dans les options du selecteur
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
