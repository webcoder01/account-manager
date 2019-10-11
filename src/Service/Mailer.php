<?php


namespace App\Service;

use Twig\Environment;


class Mailer
{
    private $mailer;
    private $twig;
    private $fileManager;

    private $subject;
    private $from;
    private $to;

    private $template;
    private $templateParams;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, FileManager $fileManager)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->fileManager = $fileManager;
    }

    /**
     * Handle exceptions
     * @param string $message
     */
    public function sendLog(string $message)
    {
        $this->subject = 'Log Money Manager';
        $this->from = getenv('MAILER_ADR_LOG');
        $this->to = getenv('MAILER_ADR_LOG');
        $this->template = 'mailer/log.html.twig';
        $this->templateParams = ['message' => $message];

        if(!$this->send()) {
            $this->fileManager->writeLog($message);
        }
    }

    /**
     * Send the email
     * @return boolean
     */
    private function send()
    {
        try {
            // Number of recipients
            $count = is_array($this->to) ? count($this->to) : 1;
            $response = null;

            $message = (new \Swift_Message($this->subject))
                ->setFrom($this->from)
                ->setTo($this->to)
                ->setBody(
                    $this->twig->render($this->template, $this->templateParams),
                    'text/html'
                );

            $response = $this->mailer->send($message);

            return $response === $count;

        } catch(\Exception $e) {
            $message = 'Unable to send the email to ' . (is_array($this->to) ? implode(' , ', $this->to) : $this->to);
            $this->fileManager->writeLog($message);
            return false;
        }
    }
}