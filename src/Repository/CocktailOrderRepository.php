<?php

namespace App\Repository;

use App\Entity\CocktailOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CocktailOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method CocktailOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method CocktailOrder[]    findAll()
 * @method CocktailOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CocktailOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CocktailOrder::class);
    }

    // /**
    //  * @return CocktailOrder[] Returns an array of CocktailOrder objects
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
    public function findOneBySomeField($value): ?CocktailOrder
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
