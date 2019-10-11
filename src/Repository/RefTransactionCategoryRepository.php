<?php

namespace App\Repository;

use App\Entity\RefTransactionCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RefTransactionCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefTransactionCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefTransactionCategory[]    findAll()
 * @method RefTransactionCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefTransactionCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RefTransactionCategory::class);
    }

    // /**
    //  * @return RefTransactionCategory[] Returns an array of RefTransactionCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RefTransactionCategory
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
