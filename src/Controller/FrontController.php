<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(ArticleRepository $articleRepository) //on injecte en dépendance le repository d'article pour pouvoir hériter des méthodes présentes dedans
    {
        // le repository est obligatoirement appelé pour les requete de SELECT

        $articles=$articleRepository->findAll();



        return $this->render('front/home.html.twig', [

            'articles'=>$articles
        ]);

    }








}
