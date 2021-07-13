<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Form\ArticleType;
use App\Form\CategorieType;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function categorie(Categorie $categorie=null, EntityManagerInterface  $manager, Request $request)
    {
        if(!$categorie):
            $categorie=new Categorie();
        endif;

        $form=$this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()):
            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash('success', 'La catégorie a bien été crée');

            return $this->redirectToRoute('listeCategorie');
        endif;
        return $this->render("back/categorie.html.twig", [
            'form'=>$form->createView()
        ]);
    }






    /**
     * @Route("/listeCategorie", name="listeCategorie")
     */
    public function listeCategorie(CategorieRepository $categorieRepository)
    {
        $categories=$categorieRepository->findAll();
        return $this->render("back/listeCategorie.html.twig", [
            'categories'=>$categories
        ]);
    }






    /**
     * @Route("/deleteCategorie/{id}", name="deleteCategorie")
     */
    public function deleteCategorie(EntityManagerInterface $manager, Categorie $categorie)
    {
        $manager->remove($categorie);
        $manager->flush();
        $this->addFlash('success', 'La catégorie a bien été suprrimée');
        return $this->redirectToRoute('listeCategorie');
    }






    /**
     * @Route("/addPanier/{id}", name="addPanier")
     */

    public function addPanier($id, PanierService $panierService)
    {
        $panierService->add($id);
        $panier=$panierService->getFullPanier();
        $total=$panierService->getTotal();

        return $this->redirectToRoute('home', [
            'panier'=>$panier,
            'total'=>$total


        ]);


    }


    /**
     * @Route("/plusPanier/{id}", name="plusPanier")
     */

    public function plusPanier($id, PanierService $panierService)
    {
        $panierService->add($id);
        $panier=$panierService->getFullPanier();
        $total=$panierService->getTotal();

        return $this->redirectToRoute('panier', [
            'panier'=>$panier,
            'total'=>$total


        ]);

    }



    /**
     * @Route("/moinsPanier/{id}", name="moinsPanier")
     */
    public function moinsPanier($id, PanierService $panierService)
    {
        $panierService->remove($id);
        $panier = $panierService->getFullPanier();
        $total = $panierService->getTotal();

        return $this->redirectToRoute('panier', [
            'panier' => $panier,
            'total' => $total


        ]);

    }





        /**
         * @Route("/deletePanier/{id}", name="deletePanier")
         */
        public function DeletePanier($id, PanierService $panierService)
    {
        $panierService->delete($id);
        $panier=$panierService->getFullPanier();
        $total=$panierService->getTotal();

        return $this->redirectToRoute('panier', [
            'panier'=>$panier,
            'total'=>$total


        ]);




    }







    /**
     * @Route("/deleteAllPanier", name="deleteAllPanier")
     */
    public function DeleteAllPanier( PanierService $panierService)
    {
        $panierService->deleteAll();
        $panier = $panierService->getFullPanier();
        $total = $panierService->getTotal();

        return $this->redirectToRoute('panier', [
            'panier' => $panier,
            'total' => $total


        ]);

    }






    /**
    * @Route("/commande", name="commande")
    */

    public function commander(PanierService $panierService, SessionInterface $session, EntityManagerInterface $manager)
    {
        $panier = $panierService->getFullPanier();
        $commande = new Commande();

        $commande->setMontantTotal($panierService->getTotal());
        $commande->setUser($this->getUser());
        $commande->setStatut((0));
        $commande->setDate(new \DateTime());

        foreach ($panier as $item):

            $article = $item['article'];
            $achat = new Achat();
            $achat->setArticle($article)
                ->setQuantite($item['quantite'])
                ->setCommande($commande);
            $manager->persist($achat);

        endforeach;

        $manager->persist($commande);
        $manager->flush();

        $panierService->deleteAll();

        $this->addFlash('success', 'votre commande a bien été pris en compte');
        return $this->redirectToRoute('listeCommande');


    }





    /**
     * @Route("/listeCommande", name="listeCommande")
     */

    public function listeCommande(CommandeRepository $commandeRepository)
    {
        $commandes=$commandeRepository->findBy(['user'=>$this->getUser()]);

        return $this->render('front/listeCommande.html.twig',[

                'commandes'=>$commandes
            ]);

    }





    /**
     * @Route("/gestionCommande", name="gestionCommande")
     */
    public function gestionCommande(CommandeRepository $commandeRepository)
    {
        $commandes=$commandeRepository->findBy([], ['statut'=>'ASC']);

        return $this->render('back/gestionCommande.html.twig',[

            'commandes'=>$commandes
        ]);


    }






    /**
     * @Route("/statut/{id}/{param}", name="statut")
     */
    public function statut(CommandeRepository $commandeRepository, EntityManagerInterface $manager, $id, $param)
    {
        $commande=$commandeRepository->find($id);

        $commande->setStatut($param);
        $manager->persist($commande);
        $manager->flush();

        return $this->redirectToRoute('gestionCommande');


    }



    /**
     * @Route("sendMail", name="sendMail")
     */
    public function sendMail(Request $request)
    {
        $transporter=(new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername('767Paris4@gmail.com')
            ->setPassword('Session767Paris4');

        $mailer=new \Swift_Mailer($transporter);

        $mess=$request->request->get('message');
        $name=$request->request->get('name');
        $surname=$request->request->get('surname');
        $subject=$request->request->get('nedd');
        $from=$request->request->get('email');

        $mess=(new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo('767Paris4@gmail.com');
        $cid=$mess->embed(\Swift_Image::fromPath('upload/diamantgood.jpg'));

        $mess->setBody(
            $this->render('mail/mail_template.html.twig',[
                    'from'=>$from,
                    'name'=>$name,
                    'surname'=>$surname,
                    'subject'=>$subject,
                    'message'=>$mess,
                    'logo'=>$cid
            ]),
            'text/html'
        );
        $mailer->send($mess);

        $this->addFlash('success', 'lemail a bien bien envoyé');
        return $this->redirectToRoute('home');

    }




    /**
     * @Route("mailForm", name="mailForm")
     */
    public function mailForm()
    {
        return $this->render('mail/mail_form.html.twig');




    }


    /**
     * @Route("mailTemplate", name="mailTemplate")
     */
    public function mailTemplate()
    {
        return $this->render('mail/mail_template.html.twig');




    }






}
