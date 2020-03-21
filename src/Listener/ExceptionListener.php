<?php

namespace App\Listener;

use App\Service\Mailer;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Handle HTTP exceptions
 *
 * @package App\Listener
 */
class ExceptionListener
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getException();
        $message = '<p style="font-weight: bold">' . $exception->getMessage() . '</p>' .
            '<p><span style="font-weight: bold">Fichier :</span>&nbsp;' . $exception->getFile() .
            '&nbsp;<span style="font-weight: bold">Ligne :</span>' . $exception->getLine() . '</p>';

        // Send an email with the error detail
        $this->mailer->sendLog($message);
    }
}