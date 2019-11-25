<?php

namespace App\Model;

use App\Interfaces\OperationDataInterface;
use App\Entity\Transaction;
use App\Entity\Income;
use App\Entity\Account;

class OperationData implements OperationDataInterface
{
    const TRANSACTION_TYPE = 'transaction';
    const INCOME_TYPE = 'income';
    
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
            default:
                $entity = null;
                break;
        }
        
        $entity->setIdAccount($account);
        
        return $entity;
    }
}
