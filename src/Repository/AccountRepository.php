<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * Find accounts from user
     * @param int $userId
     * @return array
     */
    public function findByUser(int $userId)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('a')
            ->from('App:Account', 'a')
            ->innerJoin('a.idUsersite', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('a.labelName');

        return $query->getQuery()->getResult();
    }

    /**
     * Find account of user by id
     * @param int $userId
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUserById(int $userId, int $id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('a')
            ->from('App:Account', 'a')
            ->innerJoin('a.idUsersite', 'u')
            ->where('u.id = :userId')
            ->andWhere('a.id = :id')
            ->setParameters([
                'userId' => $userId,
                'id' => $id,
            ]);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Find favorite account of user
     * @param int $userId
     * @return Account|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserFavorite(int $userId)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('a')
            ->from('App:Account', 'a')
            ->innerJoin('a.idUsersite', 'u')
            ->where('u.id = :userId')
            ->andWhere('a.isFavorite = true')
            ->setParameter('userId', $userId);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Create an account from user
     * @param string $label
     * @param int $userId
     * @param float $amount
     * @return Account|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createFromUser(string $label, int $userId, float $amount = 0.00)
    {
        $connection = $this->_em->getConnection();
        $stmt = $connection->prepare('SELECT * FROM mo.add_account(:idUser, :label, :amount)');
        $stmt->bindParam('idUser', $userId);
        $stmt->bindParam('label', $label);
        $stmt->bindParam('amount', $amount);
        $stmt->execute();
        $result = $stmt->fetchAll()[0];
        $stmt->closeCursor();

        $account = $this->find($result['id_account']);

        return $account;
    }
}
