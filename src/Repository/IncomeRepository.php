<?php

namespace App\Repository;

use App\Entity\Income;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Income|null find($id, $lockMode = null, $lockVersion = null)
 * @method Income|null findOneBy(array $criteria, array $orderBy = null)
 * @method Income[]    findAll()
 * @method Income[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Income::class);
    }
    
    /**
     * Find incomes of account
     * Incomes can be filtered by date
     * @param int $accountId
     * @param \DateTime $date
     * @return array
     */
    public function findByAccount(int $accountId, \DateTime $date = null)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('i')
            ->from('App:Income', 'i')
            ->innerJoin('i.idAccount', 'a')
            ->where('a.id = :accountId')
            ->setParameter('accountId', $accountId);

        if(null !== $date)
        {
            $minDate = clone $date;
            $maxDate = clone $date;

            $query
                ->andWhere('i.actionDate >= :minDate')
                ->andWhere('i.actionDate <= :maxDate')
                ->setParameter('minDate', $minDate->modify('first day of this month'))
                ->setParameter('maxDate', $maxDate->modify('last day of this month'))
            ;
        }

        return $query->getQuery()->getResult();
    }
    
    /**
     * Find income from user by id
     * @param int $userId
     * @param int $id
     * @return Income|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUserById(int $userId, int $id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('i')
            ->from('App:Income', 'i')
            ->innerJoin('i.idAccount',  'a')
            ->innerJoin('a.idUsersite', 'u')
            ->where('u.id = :userId')
            ->andWhere('i.id = :id')
            ->setParameters([
                'userId' => $userId,
                'id' => $id,
            ]);

        return $query->getQuery()->getOneOrNullResult();
    }
}
