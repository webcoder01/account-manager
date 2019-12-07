<?php

namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;
use App\Interfaces\OperationDataInterface;
use App\Entity\Transaction;
use App\Entity\Income;
use App\Entity\Account;
use App\Entity\Budget;

class OperationData implements OperationDataInterface
{
    const TRANSACTION_TYPE = 'transaction';
    const INCOME_TYPE = 'income';
    const BUDGET_TYPE = 'budget';
    
    public static function createEntity(string $type, Account $account)
    {
        switch($type)
        {
            case self::TRANSACTION_TYPE:
                $entity = new Transaction();
                break;
            case self::INCOME_TYPE:
                $entity = new Income();
                break;
            case self::BUDGET_TYPE:
                $entity = new Budget();
                break;
            default:
                $entity = null;
                break;
        }
        
        if(null !== $entity) {
            $entity->setIdAccount($account);
        }
        
        return $entity;
    }
    
    public static function getFormType(string $type)
    {
        switch($type)
        {
            case self::TRANSACTION_TYPE:
                $class = \App\Form\TransactionType::class;
                break;
            case self::INCOME_TYPE:
                $class = \App\Form\IncomeType::class;
                break;
            case self::BUDGET_TYPE:
                $class = \App\Form\BudgetType::class;
                break;
            default:
                $class = null;
                break;
        }
        
        return $class;
    }
    
    public static function getRepository(string $type, EntityManagerInterface $em)
    {
        switch($type)
        {
            case self::TRANSACTION_TYPE:
                $repo = $em->getRepository('App:Transaction');
                break;
            case self::INCOME_TYPE:
                $repo = $em->getRepository('App:Income');
                break;
            case self::BUDGET_TYPE:
                $repo = $em->getRepository('App:Budget');
                break;
            default:
                $repo = null;
                break;
        }
        
        return $repo;
    }
}
