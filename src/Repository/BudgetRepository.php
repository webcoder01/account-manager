<?php

namespace App\Repository;

use App\Entity\Budget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Model\Session;

/**
 * @method Budget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Budget|null findOneBy(array $criteria, array $orderBy = null)
 * @method Budget[]    findAll()
 * @method Budget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetRepository extends ServiceEntityRepository
{
    private $session;
    
    public function __construct(ManagerRegistry $registry, SessionInterface $session)
    {
        parent::__construct($registry, Budget::class);
        
        $this->session = $session;
    }

    /**
     * Get all budgets by account
     * @param int $accountId
     * @param bool $activeOnly
     * @return array
     */
    public function findByAccount(int $accountId, bool $activeOnly = false): array
    {
        $date = $this->session->get(Session::NAVIGATION_ACCOUNT);
        
        $query = $this->_em->createQueryBuilder()
                ->select('b')
                ->from('App:Budget', 'b')
                ->innerJoin('b.idAccount', 'a')
                ->where('a.id = :accountId')
                ->andWhere('b.dateMonth = :month')
                ->andWhere('b.dateYear = :year')
                ->setParameters([
                    'accountId' => $accountId,
                    'month' => $date->format('n'),
                    'year' => $date->format('Y'),
                ]);
        
        if($activeOnly) {
            $query->andWhere('b.isActive = true');
        }
        
        return $query->getQuery()->getResult();
    }
    
    /**
     * Get one budget by id by account
     * @param int $id
     * @param int $accountId
     * @return Budget|null
     */
    public function findByIdByAccount(int $id, int $accountId): ?Budget
    {
        $query = $this->_em->createQueryBuilder()
                ->select('b')
                ->from('App:Budget', 'b')
                ->innerJoin('b.idAccount', 'a')
                ->where('a.id = :accountId')
                ->andWhere('b.id = :id')
                ->setParameter('accountId', $accountId)
                ->setParameter('id', $id);
        
        return $query->getQuery()->getOneOrNullResult();
    }
    
    /**
     * Find budget from user by id
     * @param int $userId
     * @param int $id
     * @return Budget|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUserById(int $userId, int $id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('b')
            ->from('App:Budget', 'b')
            ->innerJoin('b.idAccount',  'a')
            ->innerJoin('a.idUsersite', 'u')
            ->where('u.id = :userId')
            ->andWhere('b.id = :id')
            ->setParameters([
                'userId' => $userId,
                'id' => $id,
            ]);

        return $query->getQuery()->getOneOrNullResult();
    }
}
