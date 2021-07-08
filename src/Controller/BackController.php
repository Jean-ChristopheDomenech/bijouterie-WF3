<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Form\ArticleType;
use App\Form\CategorieType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackController extends AbstractController
{









    /**
     * @Route("/addArticle", name="addArticle")
     */
    public function addArticle(Request $request, EntityManagerInterface $manager)
    {

        $article = new Article(); // ici on instancie un nouvel objet Article vide que l'on va charger avec les données du formulaire

        $form = $this->createForm(ArticleType::class, $article, array('ajout'=>true));  // on instancie un objet Form qui va va controller automatiquement la corrrespondance des champs de formulaire (contenus dans articlType) avec l'entité Article (contenu dans $article)

        $form->handleRequest($request);// la méthode handlerequest de Form nous permet de préparer la requête et remplir notre objet article instancié

        if ($form->isSubmitted() && $form->isValid()): // si le formulaire a été soumis et qu'il est valide (booléan de correspondance généré dans le createForm)
            $article->setCreateAt(new \DateTime('now'));

            $image=$form->get('image')->getData();   //on récupere l'input type File photo de notre formulaire grace à getData on obtient $_FILE dans son intégralité

            if ($image):

                $nomImage=date('YmdHis').uniqid().$image->getClientOriginalName(); //ici on modifie le nom de notre photo avec uniqid(), fonction de php générant une clé de hashage de 10 caractère aléatoires concaténé avec son nom et la date avec heure,minute et seconde pour s'assuré de l'unicité de la photo en bdd
                //et en upload

                $image->move(
                    $this->getParameter('upload_directory'),
                    $nomImage
                ); // equivalent de move_uploaded_file() en symfony attendant 2 paramètres, la direction de l'upload (défini dans config/service.yaml dans les parameters et le nom du fichier à inserer)

                $article->setImage($nomImage);

                $manager->persist($article); // le manager de symfony fait le lien entre l'entité et la BDD via l'ORM (Object Relationnal MApping) Doctrine.Grace à la méthode persist() il conserve en mémoire la requete préparé.
                $manager->flush();  //ici la méthode flush execute les requête en mémoire

                $this->addFlash('success', 'L\article a bien été ajouté');
                return $this->redirectToRoute('addArticle');
            endif;



        endif;

        return $this->render('back/addArticle.html.twig',[
            'form'=>$form->createView(),
            'article'=>$article

        ]);


    }


    /**
     * @Route("/listeArticle", name="listeArticle")
     */
    public function listeArticle(ArticleRepository $articleRepository)
    {

        $articles=$articleRepository->findAll();



        return $this->render('back/listeArticle.html.twig',[
            'articles'=>$articles

            ]

        );





    }

    /**
     * @Route("/modifArticle/{id}", name="modifArticle")
     */
    public function modifArticle(Article $article, Request $request,EntityManagerInterface $manager)
    {




        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):

            $image=$form->get('imageModif')->getData();

            if ($image):

                $nomImage=date('YmdHis').uniqid().$image->getClientOriginalName();

                $image->move(
                    $this->getParameter('upload_directory'),
                    $nomImage
                );

                //unlink($this->getParameter("upload_directory")."/".$article->getImage());

                $article->setImage($nomImage);


            endif;
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', 'Larticle a bien été modifié');
            return $this->redirectToRoute('listeArticle');



        endif;

        return $this->render('back/modifArticle.html.twig',[
                'form'=>$form->createView()

            ]

        );







    }

    /**
     * @Route("/deleteArticle/{id}", name="deleteArticle")
     */
    public function delete(Article $article, EntityManagerInterface $manager)
    {
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('success', 'Larticle a bien été supprimé');

        return $this->redirectToRoute('listeArticle');


    }



    /**
     * @Route("/ajoutCategorie", name="ajoutCategorie")
     * @Route("/modifCategorie/{id}", name="modifCategorie")
     */
    public function categorie(Categorie $categorie=null, EntityManagerInterface $manager, Request $request)
    {

        if (!$categorie):
            $categorie = new Categorie();
        endif;



        $form = $this->createForm(CategorieType::class, $categorie);

        if ($form->isSubmitted() && $form->isValid()):

            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash('succes', 'la categorie a bien été créée');


            return $this->redirectToRoute('ajoutCategorie');
            endif;
            return $this->render('back/categorie.html.twig',[

                'form'=>$form->createView()

            ]);



    }




    /**
     * @Route("/deleteCategorie/{id}", name="deleteCategorie")
     */
    //public function delete(Categorie $categorie, EntityManagerInterface $manager)
    //{
       // $manager->remove($categorie);
      //  $manager->flush();
       // $this->addFlash('success', 'La categorie a bien été supprimé');

      //  return $this->redirectToRoute('');


    //}







}
