<?php

namespace App\Repository;

use App\Entity\CocktailIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CocktailIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method CocktailIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method CocktailIngredient[]    findAll()
 * @method CocktailIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CocktailIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CocktailIngredient::class);
    }

    // /**
    //  * @return CocktailIngredient[] Returns an array of CocktailIngredient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CocktailIngredient
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
