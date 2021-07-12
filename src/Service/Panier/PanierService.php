<?php

namespace App\Service\Panier;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{


    public $session;
    public $articleRepository;

    public function __construct(SessionInterface $session, ArticleRepository $articleRepository)
    {
        $this->session = $session;
        $this->articleRepository = $articleRepository;


    }


    public function add(int $id)
    {
        $panier = $this->session->get('panier', []); //si le panier n'existe pas, il sera initialisé en session par un array vide[]

        if (!empty($panier[$id])): //s'il existe une entrée dans le panier à l'indice $id, l'article est donc deja present, on incremente dc la quantité
            $panier[$id]++;

        else://sinon on initialise à 1 la quantité
            $panier[$id] = 1;

        endif;

        $this->session->set('panier', $panier); //on charge les données dans notre session


    }


    public function remove(int $id)
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id]) && $panier[$id] > 1):
            $panier[$id]--; // si on a au minimum 2 article en panier, on decremente la quantité

        else:
            unset($panier[$id]);//sinon on vide la ligne en session

        endif;

        $this->session->set('panier', $panier);


    }


    public function delete(int $id)
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])):

            unset($panier[$id]);

        endif;

        $this->session->set('panier', $panier);

    }


    public function deleteAll()
    {

        $this->session->set('panier', []);

    }


    public function getFullPanier()
    {
        //panier[]=$id=>quantite

        $panier = $this->session->get('panier', []);

        $panierDetails = [];

        foreach ($panier as $id => $quantite):
            $panierDetails[] = [
                'article' => $this->articleRepository->find($id),
                'quantite' => $quantite,
                'totalPrixArticle'=>$this->articleRepository->find($id)->getPrix()*$quantite

            ];
        endforeach;

        return $panierDetails;

    }


    public function getTotal()
    {
        $total = 0;

        foreach ($this->getFullPanier() as $item): //$this->getFullPanier() nous retourne notre tableau $panierDetails

           $total+= $item['article']->getPrix()* $item['quantite']; //par ligne d'article différents, on multiplie le prix de l'article par sa quantite commandée que l'on incremente a notre total

        endforeach;

        return $total;


    }












}








?>