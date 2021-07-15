<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }


    public function findByPrix($prix)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.prix <= :val')
            ->setParameter('val', $prix)
            ->orderBy('a.prix', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByPrixCategorie($prix, $cat)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.prix <= :val')
            ->setParameter('val', $prix)
            ->andWhere('a.categorie = :cat')
            ->setParameter('cat', $cat)
            ->orderBy('a.prix', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function search($filter)
    {
        $builder=$this->createQueryBuilder('a');
        return $builder
            ->andWhere('a.nom LIKE :nom')
            ->setParameter('nom', '%'.$filter.'%')
            ->getQuery()
            ->getResult();


    }



    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
