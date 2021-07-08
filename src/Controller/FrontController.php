<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{

    /**
     * @route("/addArticle", name="addArticle")
     */

    public function addArticle(Request $request, EntityManagerInterface $manager)
    {
        $article = new Article(); //ici on instancie un nouvel objet "article" vide que l'on va charger avec les données du formulaire (articleType.php)

        $form = $this->createForm(ArticleType::class, $article); //on instancie un objet "form" qui va controler automatiquement la corresspondance des champs de formualire(articleType) avec l'entité article ( contenu ds $article)

        $form->handleRequest($request); //la methode handlerequest de Form nous permet de preparer la requete et remplir l'objet article instancié

        if ($form->isSubmitted() && $form->isValid()):

            $article->setCreateAt(new \DateTime('now'));

            $image = $form->get('image')->getData(); //on recupere l'input type file image de notre                  formulaire grace à getdata= on obtient $_FILE ds son integralité

            if ($image):

                $nomImage = date('YndHis') . uniqid() . $image->getClientOriginalName();
                //ici on modifie le nom de notre image avec date() uniqid() pour s'assurer de l'unicité de                 l'image en BDD et en upload (fct de php, cle de hashage de 10 caracteres alaetoire)

                $image->move(
                    $this->getParameter('upload_directory'), $nomImage
                ); //(equivalent de move_upload_file) attendant 2 parametres: la direction de l'upload(defini dans config/service.yaml dans les "parameters") et le nom du fichier à inserer

                $article->setImage($nomImage);

                $manager->persist($article);//$manager fait le lien entre l'entité et la BDD via l'ORM (Object Relational Mapping) Doctrine. Grace à la methode persist, il conserve en memoire la requete preparée
                $manager->flush(); //cette methode execute les requetes en memoires

                $this->addFlash('success', 'larticle a bien été ajouté');
                return $this->redirectToRoute('addArticle');

            endif;


        endif;

        return $this->render('front/addArticle.html.twig', [

            'form' => $form->createView()

        ]);


    }


}
