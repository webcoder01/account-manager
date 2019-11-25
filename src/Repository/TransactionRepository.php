<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * Find transactions of account
     * Transactions can be filtered by date
     * @param int $accountId
     * @param \DateTime $date
     * @return array
     */
    public function findByAccount(int $accountId, \DateTime $date = null)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('t')
            ->from('App:Transaction', 't')
            ->innerJoin('t.idAccount', 'a')
            ->where('a.id = :accountId')
            ->orderBy('t.actionDate', 'desc')
            ->setParameter('accountId', $accountId);

        if(null !== $date)
        {
            $minDate = clone $date;
            $maxDate = clone $date;

            $query
                ->andWhere('t.actionDate >= :minDate')
                ->andWhere('t.actionDate <= :maxDate')
                ->setParameter('minDate', $minDate->modify('first day of this month'))
                ->setParameter('maxDate', $maxDate->modify('last day of this month'))
            ;
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Find transaction from user by id
     * @param int $userId
     * @param int $id
     * @return Transaction|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUserById(int $userId, int $id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('t')
            ->from('App:Transaction', 't')
            ->innerJoin('t.idAccount',  'a')
            ->innerJoin('a.idUsersite', 'u')
            ->where('u.id = :userId')
            ->andWhere('t.id = :id')
            ->setParameters([
                'userId' => $userId,
                'id' => $id,
            ]);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Find transactions by their ids
     * @param array $ids
     * @return array
     */
    public function findByIds(array $ids)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('t')
            ->from('App:Transaction', 't')
            ->where('t.id in (:values)')
            ->setParameter('values', $ids);

        return $query->getQuery()->getResult();
    }
    
    public function getTotalAmountByAccountByMonth(int $accountId, \DateTime $date)
    {
        $minDate = clone $date;
        $maxDate = clone $date;
            
        $query = $this->_em->createQueryBuilder()
                ->select('sum(t.amount)')
                ->from('App:Transaction', 't')
                ->innerJoin('t.idAccount', 'a')
                ->where('a.id = :accountId')
                ->andWhere('t.actionDate >= :minDate')
                ->andWhere('t.actionDate <= :maxDate')
                ->setParameters([
                    'accountId' => $accountId,
                    'minDate' => $minDate->modify('first day of this month'),
                    'maxDate' => $maxDate->modify('last day of this month'),
                ]);
        
        return $query->getQuery()->getSingleScalarResult();
    }
}
