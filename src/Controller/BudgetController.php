<?php


namespace App\Controller;


use App\Model\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BudgetController extends AbstractController
{
    public function view(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $accountRepo = $em->getRepository('App:Account');
        $idAccount = $request->getSession()->get(Session::ID_ACCOUNT);

        // Get favorite account if no ID is defined
        $account = null === $idAccount ?
            $accountRepo->findUserFavorite($user->getId()) : $accountRepo->findByUserById($user->getId(), $idAccount);
        if(null === $account) {
            throw new \Exception('Account not found');
        }
        
    }
}