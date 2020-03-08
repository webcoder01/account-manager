<?php


namespace App\Utils;


class Constants
{
    const FLASH_SUCCESS = 'info';
    const FLASH_WARNING = 'danger';
    const FLASH_NOTICE = 'warning';

    const RESET_TOKEN_LIFETIME = 86400; // 24 hours

    /**
     * Get all the constants of class
     *
     * @return array
     */
    public function getConstants(): array
    {
        try {
            $reflectionClass = new \ReflectionClass($this);
            return $constants = $reflectionClass->getConstants();

        } catch (\Exception $e) {
            return [];
        }
    }
}