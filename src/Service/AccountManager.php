<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Account;
use App\Model\Session;

class AccountManager
{
    private $em;
    private $session;
    private $token;
    
    public function __construct(EntityManagerInterface $em, SessionInterface $session, TokenStorageInterface $token)
    {
        $this->em = $em;
        $this->session = $session;
        $this->token = $token;
    }
    
    public function getAccountFromSession(): ?Account
    {
        $user = $this->token->getToken()->getUser();
        $accountId = $this->session->get(Session::ID_ACCOUNT, 0);
        $entity = $this->em->getRepository('App:Account')->findByUserById($user->getId(), $accountId);
        
        return $entity;
    }
    
    /**
     * Returns array for twig from the account stored in session
     * @return array
     */
    public function getTwigParamsForNavigation(): array
    {
        $date = $this->session->get(Session::NAVIGATION_ACCOUNT);
        
        return [
            'year' => $date->format('Y'),
            'month' => $date->format('n'),
            'id' => $this->session->get(Session::ID_ACCOUNT),
        ];
    }
}
