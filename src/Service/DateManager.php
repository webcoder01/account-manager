<?php

namespace App\Service;

class DateManager
{
    const MONTHS = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    /**
     * @param \DateTime $date
     *
     * @return \DateTime
     */
    public static function getPreviousMonthFromDate(\DateTime $date): \DateTime
    {
        $new = clone $date;
        
        return $new->modify('-1 month');
    }

    /**
     * @param \DateTime $date
     *
     * @return \DateTime
     */
    public static function getNextMonthFromDate(\DateTime $date): \DateTime
    {
        $new = clone $date;
        
        return $new->modify('+1 month');
    }
    
    /**
     * Return days for options of select input
     *
     * @param \DateTime $date
     *
     * @return array
     */
    public static function getDayOptions(\DateTime $date): array
    {
        $days = [];
        $string = $date->format('Y-m');

        for($i = 1; $i <= intval($date->format('t')); $i++) {
            $days[strval($i)] = $string . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        return $days;
    }
}
