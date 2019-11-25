<?php

namespace App\Interfaces;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Entity\Income;

interface OperationDataInterface
{
    /**
     * Create a Transaction or Income entity
     * @param string $type
     * @param Account $account
     * @return Transaction|Income|null
     */
    public static function createEntity(string $type, Account $account);
}
