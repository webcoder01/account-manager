<?php


namespace App\Controller;


use App\Model\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Budget;
use App\Form\BudgetType;
use App\Service\AccountManager;
use Symfony\Component\HttpFoundation\Response;

class BudgetController extends AbstractController
{
    const STATUT_ENABLE = 'enable';
    const STATUT_DISABLE = 'disable';
    
    public function view(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $accountRepo = $em->getRepository('App:Account');
        $idAccount = $request->getSession()->get(Session::ID_ACCOUNT);
        $session = $request->getSession();

        // Get favorite account if no ID is defined
        $account = null === $idAccount ?
            $accountRepo->findUserFavorite($user->getId()) : $accountRepo->findByUserById($user->getId(), $idAccount);
        if(null === $account) {
            throw new \Exception('Account not found');
        }
        
        $operations = $em->getRepository('App:Budget')->findBy(['idAccount' => $account, 'isActive' => true,]);
        $newOp = new Budget();
        $form = $this->createForm(BudgetType::class, $newOp);
        if($request->isMethod('post'))
        {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $newOp->setIdAccount($account);
                $em->persist($newOp);
                $em->flush();
                $date = $session->get(Session::NAVIGATION_ACCOUNT);
                
                return $this->redirectToRoute('account.view', [
                    'year' => $date->format('Y'),
                    'month' => $date->format('n'),
                    'id' => $account->getIdAccount()->getId(),
                ]);
            }
        }
        
        return $this->render('budget/view.html.twig', [
            'form' => $form->createView(),
            'operations' => $operations,
        ]);
    }
    
    /**
     * Enable or disable the budget
     * @param Request $request
     * @param AccountManager $accountManager
     * @param type $id
     * @return Response
     * @throws \Exception
     */
    public function manage(Request $request, AccountManager $accountManager, $id)
    {
        $status = $request->get('status', null);
        if(null === $status) {
            throw new \Exception('Budget status undefined');
        }
        
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $budget = $em->getRepository('App:Budget')->findByIdByAccount(intval($id), $session->get(Session::ID_ACCOUNT));
        if(null === $budget) {
            throw new \Exception('Budget not found');
        }
        
        switch($status)
        {
            case self::STATUT_ENABLE:
                $budget->setIsActive(true);
                break;
            case self::STATUT_DISABLE:
                $budget->setIsActive(false);
                break;
            default:
                break;
        }
        
        $em->flush();
        
        return $this->redirectToRoute('account.view', $accountManager->getTwigParamsForNavigation());
    }
}