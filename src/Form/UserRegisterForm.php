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
                'label' => 'Nom'
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
            ])

            ->add('CGU', CheckboxType::class, [
                'label' => 'J\'accepte les CGU de GreenGoodies',
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue(
                        message: 'Vous devez accepter les CGU de GreenGoodies pour vous inscrire.'
                    )],
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
