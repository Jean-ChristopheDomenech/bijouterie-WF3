<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir le username"
                ]
            ])
            ->add('email', TextType::class, [
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir lemail"
                ]
            ])
            ->add('password', PasswordType::class, [
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir le password"
                ]
            ])



            ->add('nom', TextType::class, [
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir le mdp"
                ]
            ])
            ->add('prenom', TextType::class, [
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir le prenom"
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                "required"=>false,
                "label"=>false,
                "attr"=>[
                    "placeholder"=>"Veuillez saisir le confirmPassword"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
