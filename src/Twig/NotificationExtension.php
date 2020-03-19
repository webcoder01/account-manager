<?php

namespace App\Twig;

use App\Utils\Constants;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_notifications', [$this, 'getNotifications']),
        ];
    }

    /**
     * Returns flash constants
     *
     * @return array
     */
    public function getNotifications(): array
    {
        $constants = new Constants();
        $flashes = [];

        foreach ($constants->getConstants() as $constant) {
            $pos = strpos($constant, '_');
            if(false !== $pos && 'FLASH' === substr($constant, 0, ($pos - 1))) {
                $flashes[] = $constant;
            }
        }

        return $flashes;
    }
}
