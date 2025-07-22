<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class UserRegisterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez renseigner votre nom'),
                    new Assert\Length(
                        min: 4,
                        max: 255,
                        minMessage: 'Votre nom doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Votre nom ne peut contenir plus de {{ limit }} caractères.'
                    ),
                    new Assert\Regex(
                        pattern: '/^[a-zA-Z]+$/',
                        message: 'Votre nom ne peut contenir que des lettres.'
                    ),
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez renseigner votre prénom'),
                    new Assert\Length(
                        min: 4,
                        max: 255,
                        minMessage: 'Votre prénom doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Votre prénom ne peut contenir plus de {{ limit }} caractères.'
                    ),
                    new Assert\Regex(
                        pattern: '/^[a-zA-Z]+$/',
                        message: 'Votre prénom ne peut contenir que des lettres.'
                    ),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez renseigner votre adresse email'),
                    new Assert\Email(message: 'Veuillez saisir une adresse email valide'),
                ],

            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez renseigner votre mot de passe'),
                    new Assert\Regex(
                        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%!&*?])[A-Za-z\d@#$%!&*?]{8,}$/',
                        message: 'Votre mot de passe doit comporter au moins 8 caractères, avec au minimum une majuscule, une minuscule, un chiffre et un caractère spécial.'
                    ),
                ]
            ])

            ->add('CGU', CheckboxType::class, [
                'label' => 'J\'accepte les CGU de GreenGoodies',
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue(message: 'Vous devez accepter les CGU de GreenGoodies pour vous inscrire.')
                ],
            ])

            ->add('submit', SubmitType::class, [
                "label" => "S'inscrire",
                "attr" => ["class" => "base-button"]
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
