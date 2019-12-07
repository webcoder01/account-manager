<?php

namespace App\Interfaces;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Entity\Income;

interface OperationDataInterface
{
    /**
     * Create a Transaction or Income entity
     * @param string $type
     * @param Account $account
     * @return Transaction|Income|Budget|null
     */
    public static function createEntity(string $type, Account $account);
    
    /**
     * Returns the form type
     * @param string $type
     * @return TransactionType|IncomeType|BudgetType|null
     */
    public static function getFormType(string $type);
    
    /**
     * Returns the repository
     * @param string $type
     * @param EntityManagerInterface $em
     * @return TransactionRepository|IncomeRepository|BudgetRepository|null
     */
    public static function getRepository(string $type, EntityManagerInterface $em);
}
