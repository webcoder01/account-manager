<?php

namespace App\Repository;

use App\Entity\Budget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Budget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Budget|null findOneBy(array $criteria, array $orderBy = null)
 * @method Budget[]    findAll()
 * @method Budget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Budget::class);
    }

    public function findByAccount(int $accountId): array
    {
        $query = $this->_em->createQueryBuilder()
                ->select('b')
                ->from('App:Budget', 'b')
                ->innerJoin('b.idAccount', 'a')
                ->where('a.id = :accountId')
                ->andWhere('b.isActive = true')
                ->setParameter('accountId', $accountId);
        
        return $query->getQuery()->getResult();
    }
}
