<?php


namespace App\Service;


use App\Entity\Usersite;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Security
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * Generate token for reset password requests
     *
     * @param Usersite $user
     *
     * @return string
     * @throws \Exception
     */
    public function getResetPasswordToken(Usersite $user): string
    {
        $algorithm = $this->parameterBag->get('encoder_algorithm');
        $date = new \DateTime();

        return hash($algorithm, ($user->getId() . $user->getSalt() . $date->getTimestamp()));
    }
}