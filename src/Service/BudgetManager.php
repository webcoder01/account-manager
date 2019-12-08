<?php

namespace App\Service;

use App\Entity\Budget;

class BudgetManager
{
    /**
     * Group budgets by their status
     * @param array $budgets
     * @return array|false
     */
    public function groupBudgetsByStatus(array $budgets): array
    {
        $groups = ['enabled' => [], 'disabled' => []];
        $isCorrect = true;

        foreach($budgets as $budget)
        {
            if(!$budget instanceof Budget) {
                $isCorrect = false;
                break;
            }
            
            $groups[($budget->getIsActive() ? 'enabled' : 'disabled')][] = $budget;
        }
        
        return !$isCorrect ? $isCorrect : $groups;
    }
}
