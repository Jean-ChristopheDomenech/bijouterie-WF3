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
    public function registration(EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {

        //UserPasswordEncoderInterface  pour pouvoir fonctionner attends l'objet User, que celui ci herite de la class UserInterface, qui attend des méthode
        //bien spécifiques à implementer afin de s'assurer du bon fonctionnement de l'authentification

        $user=new User();

        $form=$this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()):


            $hash=$user->getPassword();
        $hashpassword=$encoder->encodePassword($user, $hash);

        $user->setPassword($hashpassword);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'Félicitation votre compte a bien été créé, connectez vous à présent');
        return $this->redirectToRoute('home');
            endif;



        return $this->render('security/registration.html.twig',[
           'form'=>$form->createView()

        ]);

    }


    /**
     *@Route("/login", name="login")
     */
    public function login()
    {


        return $this->render('security/login.html.twig');
    }

    /**
     * @Route ("/logout", name="logout")
     */
    public function logout()
    {

    }




}
