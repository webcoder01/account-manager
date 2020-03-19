<?php


namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Mailer
{
    private $mailer;
    private $twig;
    private $fileManager;
    private $router;

    private $subject;
    private $from;
    private $to;

    private $template;
    private $templateParams;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, FileManager $fileManager, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->fileManager = $fileManager;
        $this->router = $router;
    }

    /**
     * Handle exceptions
     *
     * @param string $message
     */
    public function sendLog(string $message)
    {
        $this->subject = 'Log Money Manager';
        $this->from = getenv('MAILER_EMAIL');
        $this->to = getenv('MAILER_EMAIL');
        $this->template = 'mailer/log.html.twig';
        $this->templateParams = ['message' => $message];

        if (!$this->send()) {
            $this->fileManager->writeLog($message);
        }
    }

    /**
     * Send an email for resetting the user's password
     *
     * @param string $email
     * @param string $token
     *
     * @return boolean
     */
    public function sendResetRequest(string $email, string $token): bool
    {
        $this->subject = getenv('APP_OWNER') . ' : réinitialisation du mot de passe';
        $this->from = getenv('MAILER_EMAIL');
        $this->to = strtolower(trim($email));
        $this->template = 'mailer/reset.request.html.twig';
        $this->templateParams = [
            'url' => $this->router->generate('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL)
        ];

        return $this->send();
    }

    /**
     * Send the email
     *
     * @return boolean
     */
    private function send(): bool
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

        } catch (\Exception $e) {
            $message = 'Unable to send the email to ' . (is_array($this->to) ? implode(' , ', $this->to) : $this->to);
            $this->fileManager->writeLog($message);
            return false;
        }
    }
}