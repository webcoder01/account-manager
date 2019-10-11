<?php


namespace App\Listener;


use App\Service\Mailer;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

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

        // Send an email with the error detail
        $this->mailer->sendLog($exception->getMessage());
    }
}