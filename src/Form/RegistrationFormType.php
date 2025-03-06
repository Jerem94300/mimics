<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email' , TextType::class, [
                'required' => false,
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre Email.',
                    ]),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'label' => "J'accepte les conditions générales",
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions générales.',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => "Les mots de passe ne correspondent pas",
                'required' => false,
                'first_options' => ['label' => "Mot de passe"],
                'second_options' => ['label' => "Confirmez votre mot de passe"],
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir 8 caractères minimum',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!#%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                        'match' => true,
                        'message' => "Le mot de passe doit contenir une minuscule, une majuscule, un chiffre et un caractère spécial"
                    ]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'required' => false,
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre prénom.',
                    ]),
                ],


            ]) 
            ->add('lastName' , TextType::class, [
                'required' => false,
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre nom.',
                    ]),
                ],


            ])
            ->add('city' , TextType::class, [
                'required' => false,
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre ville.',
                    ]),
                ],


            ])
            ->add('phone' , TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre numéro de téléphone.',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le numéro de téléphone n\'est pas au bon format ',
                        // max length allowed by Symfony for security reasons
                        'max' => 10,
                    ]),
                ],


            ])
            ->add('zipcode' , TextType::class, [
                'required' => false,
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre code postal.',
                    ]),
                ],


            ])
            ->add('address' , TextType::class, [
                'required' => false,
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre adresse.',
                    ]),
                ],


            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
