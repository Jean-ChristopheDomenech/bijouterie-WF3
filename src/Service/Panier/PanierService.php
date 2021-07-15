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


    public function add(int $id, $param=null)
    {
        $panier = $this->session->get('panier', []); // si le panier n'existe pas il sera initialisé en session par un array vide []

        if (!empty($panier[$id])): // si il esxiste une entrée dans panier à l'indice $id, l'article est donc déjà présent, on incrémente donc la quantité
            $panier[$id]++;
        else: // sinon on l'initialise à 1 en quantité

            $panier[$id] = 1;

        endif;

        $this->session->set('panier', $panier); //on charg à présent les données dans notre session

    }

    public function remove(int $id)
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id]) && $panier[$id]>1):
        $panier[$id]--; // si on a au minimum 2 articles en panier, on décrémente la quantité

        else:
        unset($panier[$id]); //sinon on vide la ligne en session
        endif;

        $this->session->set('panier',$panier);

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
     // panier[]= $id=>quantité

        $panier=$this->session->get('panier', []);

        $panierDetail=[];

        foreach ($panier as $id => $quantite):
            $panierDetail[]=[
              'article'=>$this->articleRepository->find($id),
              'quantite'=>$quantite

            ];

            endforeach;

            return $panierDetail;

    }

    public function getTotal()
    {


      $total=0;


      foreach ($this->getFullPanier() as $item): // $this->>getFullPanier() nous retourne notre tableau multidimmensionnel $panierDetail

          $total += $item['article']->getPrix()* $item['quantite']; // par ligne d'article différents, on multipli le prix de l'article par la quantité commandé que l'on incrémente à notre total

          endforeach;

          return $total;


    }








}