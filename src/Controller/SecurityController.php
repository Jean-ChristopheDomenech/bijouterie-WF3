<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{


    /**
     * @Route("/register", name="register")
     */
    public function registration(EntityManagerInterface  $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {
        //UserPasswordEncoderInterface pour pouvoir fonctioner attends l'objet User, que celui ci herite de la classe Userinterface, qui attends des methodes bien specifiques à implementer afin de s'assurer du bon fonctionnement de l'authentification

        $user= new User();
        $form=$this->createForm(RegistrationType::class, $user);




    }
}
