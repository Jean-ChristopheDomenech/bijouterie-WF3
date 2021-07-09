<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{


    /**
     * @Route("/register", name="register")
     */
    public function registration(EntityManagerInterface $manager, Request $request, UserPasswordHasherInterface $encoder)
    {
        //UserPasswordEncoderInterface pour pouvoir fonctioner attends l'objet User, que celui ci herite de la classe Userinterface, qui attends des methodes bien specifiques à implementer afin de s'assurer du bon fonctionnement de l'authentification

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()):

            $hash = $user->getPassword();
            $hashpassword = $encoder->hashPassword($user, $hash);

            $user->setPassword($hashpassword);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'vous etes inscrits');
            return $this->redirectToRoute('home');


        endif;


        return $this->render("security/registration.html.twig", [
            'form' => $form->createView()
        ]);


    }


    /**
     * @Route("/login", name="login")
     */
    public function login()
    {


        return $this->render("security/login.html.twig", [

        ]);


    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {


        return $this->render("security/logout.html.twig", [

        ]);


    }


}
