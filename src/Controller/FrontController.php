<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(ArticleRepository $articleRepository, CategorieRepository $categorieRepository, Request $request , PaginatorInterface $paginator, SessionInterface $session) //on injecte en dépendance le repository d'article pour pouvoir hériter des méthodes présentes dedans
    {
        // le repository est obligatoirement appelé pour les requete de SELECT
        $categories=$categorieRepository->findAll();
        if ($_POST):

            $session->set('articles', '');

            $cat = $request->request->get('categorie');
            $prix = $request->request->get('prixmax');

            if ($cat == 'all' && $prix !== 1500):
                $articles = $articleRepository->findByPrix($prix);
            elseif ($cat !== 'all' && $prix == 1500):
                $articles = $articleRepository->findBy(['categorie' => $cat]);
            elseif ($cat!='all' && $prix!=1500):
                $articles=$articleRepository->findByPrixCategorie($prix, $cat);

                else:
                    $articles=$articleRepository->findAll();
            endif;

            $session->set('articles', $articles);
            $articles=$session->get('articles');
            $articles=$paginator->paginate(
                $articles,
                $request->query->getInt('page', 1),
                6
            );


            return $this->render('front/home.html.twig', [

                'articles'=>$articles,
                'categories'=>$categories
            ]);
        endif;

        $articles = $articleRepository->findAll();
        $articles=$paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('front/home.html.twig', [
            'categories' => $categories,
            'articles' => $articles
        ]);

    }

    /**
     * @Route ("/panier", name="panier")
     */
    public function panier(PanierService $panierService)
    {
        $panier = $panierService->getFullPanier();
        $total = $panierService->getTotal();

        return $this->render("front/panier.html.twig", [
            'panier' => $panier,
            'total' => $total

        ]);
    }


    /**
     * @Route ("/searchRender", name="searchRender")
     */
    public function search(Request $request, ArticleRepository $repository, CategorieRepository $repositorycat, PaginatorInterface $paginator, SessionInterface $session)
    {
        $session->set('articles', '');
        $search=$request->query->get('search');

        $articles=$repository->search($search);
        $categories=$repositorycat->findAll();


        return $this->render("front/home.html.twig",[

            'articles'=>$articles,
            'categories'=>$categories

            ]);
    }





}
