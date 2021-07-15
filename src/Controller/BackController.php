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
use App\Repository\UserRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Uuid;

class BackController extends AbstractController
{


    /**
     *
     * @Route("/addArticle", name="addArticle")
     */
    public function addArticle(Request $request, EntityManagerInterface $manager)
    {

        $article = new Article(); // ici on instancie un nouvel objet Article vide que l'on va charger avec les données du formulaire

        $form = $this->createForm(ArticleType::class, $article, array('ajout' => true));  // on instancie un objet Form qui va va controller automatiquement la corrrespondance des champs de formulaire (contenus dans articlType) avec l'entité Article (contenu dans $article)

        $form->handleRequest($request);// la méthode handlerequest de Form nous permet de préparer la requête et remplir notre objet article instancié

        if ($form->isSubmitted() && $form->isValid()): // si le formulaire a été soumis et qu'il est valide (booléan de correspondance généré dans le createForm)
            $article->setCreateAt(new \DateTime('now'));

            $image = $form->get('image')->getData();   //on récupere l'input type File photo de notre formulaire grace à getData on obtient $_FILE dans son intégralité

            if ($image):

                $nomImage = date('YmdHis') . uniqid() . $image->getClientOriginalName(); //ici on modifie le nom de notre photo avec uniqid(), fonction de php générant une clé de hashage de 10 caractère aléatoires concaténé avec son nom et la date avec heure,minute et seconde pour s'assuré de l'unicité de la photo en bdd
                //et en upload

                $image->move(
                    $this->getParameter('upload_directory'),
                    $nomImage
                ); // equivalent de move_uploaded_file() en symfony attendant 2 paramètres, la direction de l'upload (défini dans config/service.yaml dans les parameters et le nom du fichier à inserer)

                $article->setImage($nomImage);

                $manager->persist($article); // le manager de symfony fait le lien entre l'entité et la BDD via l'ORM (Object Relationnal MApping) Doctrine.Grace à la méthode persist() il conserve en mémoire la requete préparé.
                $manager->flush();  //ici la méthode flush execute les requête en mémoire

                $this->addFlash('success', 'L\'article a bien été ajouté');
                return $this->redirectToRoute('listeArticle');
            endif;


        endif;

        return $this->render('back/addArticle.html.twig', [
            'form' => $form->createView()

        ]);


    }


    /**
     * @Route("/listeArticle", name="listeArticle")
     */
    public function listeArticle(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();


        return $this->render('back/listeArticle.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     *
     * @Route("/modifArticle/{id}", name="modifArticle")
     */
    public function modifArticle(Article $article, Request $request, EntityManagerInterface $manager)
    {
        // lorsqu'un id est transité dans l'url et qu'une entité est injectée en dépendance, symfony instancie automatique l'objet entité et le rempli avec ses données en BDD. Pas besoin d'utiliser la méthode find($id) du repository

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):

            $photo = $form->get('photoModif')->getData();

            if ($photo):

                $nomPhoto = date('YmdHis') . uniqid() . $photo->getClientOriginalName();

                $photo->move(
                    $this->getParameter('upload_directory'),
                    $nomPhoto
                );

                unlink($this->getParameter('upload_directory') . '/' . $article->getPhoto());

                $article->setPhoto($nomPhoto);


            endif;
            $manager->persist($article);
            $manager->flush();
            $this->addFlash('success', 'L\'article a bien été modifié');
            return $this->redirectToRoute('listeArticle');


        endif;

        return $this->render('back/modifArticle.html.twig', [

            'form' => $form->createView(),
            'article' => $article
        ]);


    }


    /**
     * @Route("/deleteArticle/{id}", name="deleteArticle")
     */
    public function deleteArticle(Article $article, EntityManagerInterface $manager)
    {
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('success', 'L\'article a bien été supprimé');

        return $this->redirectToRoute('listeArticle');
    }


    /**
     * @Route("/ajoutCategorie", name="ajoutCategorie")
     * @Route("/modifCategorie/{id}", name="modifCategorie")
     */
    public function categorie(Categorie $categorie = null, EntityManagerInterface $manager, Request $request)
    {

        if (!$categorie):
            $categorie = new Categorie();
        endif;


        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()):

            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash('success', 'La catégorie a bien été créée');

            return $this->redirectToRoute('listeCategorie');

        endif;

        return $this->render("back/categorie.html.twig", [

            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/listeCategorie", name="listeCategorie")
     */
    public function listeCategorie(CategorieRepository $categorieRepository)
    {

        $categories = $categorieRepository->findAll();

        return $this->render("back/listeCategorie.html.twig", [
            'categories' => $categories

        ]);
    }

    /**
     * @Route("/deleteCategorie/{id}", name="deleteCategorie")
     */
    public function deleteCategorie(EntityManagerInterface $manager, Categorie $categorie)
    {
        $manager->remove($categorie);
        $manager->flush();
        $this->addFlash('success', 'La catégorie a bien été supprimée');
        return $this->redirectToRoute('listeCategorie');


    }


    /**
     * @Route("/addPanier/{id}/{param}", name="addPanier")
     */
    public function addPanier(PanierService $panierService, $id, $param)
    {

        $panierService->add($id);
        $panier = $panierService->getFullPanier();
        $total = $panierService->getTotal();


        if ($param !== 'home'):
            return $this->redirectToRoute('panier', [
                'panier' => $panier,
                'total' => $total

            ]);
        else:
            return $this->redirectToRoute('home', [
                'panier' => $panier,
                'total' => $total

            ]);
        endif;

    }


    /**
     * @Route("/removePanier/{id}", name="removePanier")
     */
    public function removePanier($id, PanierService $panierService)
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
    public function deletePanier($id, PanierService $panierService)
    {

        $panierService->delete($id);
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
    public function commande(PanierService $panierService, SessionInterface $session, EntityManagerInterface $manager)
    {

        $panier = $panierService->getFullPanier();

        $commande = new Commande();
        $commande->setMontantTotal($panierService->getTotal());
        $commande->setUser($this->getUser());
        $commande->setStatut(0);
        $commande->setDate(new \DateTime());

        foreach ($panier as $item):
            $article = $item['article'];
            $achat = new Achat();
            $achat->setArticle($article);
            $achat->setQuantite($item['quantite']);
            $achat->setCommande($commande);

            $manager->persist($achat);
        endforeach;
        $manager->persist($commande);

        $manager->flush();

        $panierService->deleteAll();

        $this->addFlash('success', 'Votre commande a été prise en compte');

        return $this->redirectToRoute('listeCommande');

    }

    /**
     * @Route("/listeCommande", name="listeCommande")
     */
    public function listeCommande(CommandeRepository $commandeRepository)
    {

        $commandes=$commandeRepository->findBy(['user'=>$this->getUser()]);

        return $this->render('front/listeCommande.html.twig',[

            'commandes'=>$commandes]);


    }

    /**
     * @Route ("/gestionCommande", name="gestionCommande")
     */
    public function gestionCommande(CommandeRepository $commandeRepository)
    {

        $commandes=$commandeRepository->findBy([], ['statut'=>'ASC']);

        return $this->render('back/gestionCommande.html.twig',[

            'commandes'=>$commandes]);

    }

    /**
     * @Route ("/statut/{id}/{param}", name="statut")
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
     * @Route ("/sendMail", name="sendMail")
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
        $subject=$request->request->get('need');
        $from=$request->request->get('email');

        $message=(new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo('767Paris4@gmail.com');

        $cid=$message->embed(\Swift_Image::fromPath('upload/logo.jpg'));
        $message->setBody(
            $this->renderView('mail/mail_template.html.twig',[
                'from'=>$from,
                'name'=>$name,
                'surname'=>$surname,
                'subject'=>$subject,
                'message'=>$mess,
                'logo'=>$cid,
                'objectif'=>'Accéder au site',
                'liens'=>'http://127.0.0.1:8000'

            ]),
            'text/html'

        );
        $mailer->send($message);

        $this->addFlash('success', 'l\'email a bien été transmis');
        return $this->redirectToRoute('home');




    }

    /**
     * @Route("/mailForm", name="mailForm")
     */
    public function mailForm()
    {
        return $this->render('mail/mail_form.html.twig');
    }


    /**
     * @Route("/mailTemplate", name="mailTemplate")
     */
    public function mailTemplate()
    {
        return $this->render('mail/mail_template.html.twig');
    }


    /**
     * @Route("/forgotPassword", name="forgotPassword")
     */
    public function forgotPassword(Request $request, UserRepository $repository, EntityManagerInterface $manager)
    {

        if ($_POST):

            $email=$request->request->get('email');

            $user=$repository->findOneBy(['email'=>$email]);


            if ($user):
                $transporter=(new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
                    ->setUsername('767Paris4@gmail.com')
                    ->setPassword('Session767Paris4');

                $mailer=new \Swift_Mailer($transporter);

                $mess="Vous avez fait une demande de réinitialisation de mot de passe, veuillez cliquez sur le liens ci-dessous ";
                $name="";
                $surname="";
                $subject="Mot de passe oublié";
                $from='767Paris4@gmail.com';

                $message=(new \Swift_Message($subject))
                    ->setFrom($from)
                    ->setTo($email);

                $mail=urlencode($email);
                $token=uniqid();

                $user->setReset($token);
                $manager->persist($user);
                $manager->flush();



                $cid=$message->embed(\Swift_Image::fromPath('upload/logo.jpg'));
                $message->setBody(
                    $this->renderView('mail/mail_template.html.twig',[
                        'from'=>$from,
                        'name'=>$name,
                        'surname'=>$surname,
                        'subject'=>$subject,
                        'message'=>$mess,
                        'logo'=>$cid,
                        'objectif'=>'Réinitialiser',
                        'liens'=>'http://127.0.0.1:8000/resetToken/'.$mail.'/'.$token

                    ]),
                    'text/html'

                );
                $mailer->send($message);

                $this->addFlash('success', 'Un liens de réinitialisation vous a été envoyé à votre adresse mail');
                $this->redirectToRoute('forgotPassword');

            else:

                $this->addFlash('error', 'Aucun utilisateur ne correspond à cet Email');
                $this->redirectToRoute('forgotPassword');
            endif;



        endif;


        return $this->render('security/forgotPassword.html.twig');

    }

    /**
     * @Route("/resetToken/{email}/{token}")
     */
    public function resetToken($email, $token, UserRepository $repository)
    {


        $mail=urldecode($email);
        $user=$repository->findOneBy(['email'=>$mail, 'reset'=>$token]);
        //dd($mail);
        if ($user):
            return $this->redirectToRoute('resetPassword',[
                'id'=>$user->getId()
            ]);
        else:
            $this->addFlash('error', 'Une erreur s\'est produite, veuillez refaire une demande de réinitialisation');
            return $this->redirectToRoute('login');
        endif;

    }

    /**
     * @Route("/resetPassword", name="resetPassword")
     */
    public function resetPassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, UserRepository $repository)
    {

        if ($_POST):
            $password=$request->request->get('password');
            $confirmPassword=$request->request->get('confirm_password');
            $id=$request->request->get('id');
            if ($password == $confirmPassword):
                $user=$repository->find($id);
                $hash=$encoder->encodePassword($user, $password);
                $user->setPassword($hash);
                $user->setReset(null);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été modifié, connectez vous à présent');
                return $this->redirectToRoute('login');
            else:
                $this->addFlash('error', 'Vos mot de passe ne correspondent pas, veuillez les saisir à nouveau');
                return $this->redirectToRoute('resetPassword',[
                    'id'=>$id
                ]);
            endif;

        endif;

        return $this->render('security/resetPassword.html.twig');
    }




}
