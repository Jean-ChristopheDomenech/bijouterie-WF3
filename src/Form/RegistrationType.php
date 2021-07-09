<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                "required" => false,
                "label" => false,
                "attr" => [
                    "placeholder" => "Veuillez saisir votre pseudo"
                ]
            ])
            ->add('email', TextType::class, [
                "required" => false,
                "label" => false,
                "attr" => [
                    "placeholder" => "Veuillez saisir votre email"
                ]
            ])
            ->add('password', PasswordType::class, [
                "required" => false,
                "label" => false,
                "attr" => [
                    "placeholder" => "Veuillez saisir un mot de passe"
                ]
            ])
            ->add('nom', TextType::class, [
                "required" => false,
                "label" => false,
                "attr" => [
                    "placeholder" => "Veuillez saisir votre nom"
                ]
            ])
            ->add('prenom', TextType::class, [
                "required" => false,
                "label" => false,
                "attr" => [
                    "placeholder" => "Veuillez saisir votre prenom"
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                "required" => false,
                "label" => false,
                "attr" => [
                    "placeholder" => "Veuillez confirmer votre mot de passe"
                ]
            ])
            ->add('valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
