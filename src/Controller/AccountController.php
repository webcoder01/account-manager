<?php


namespace App\Controller;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Form\AccountType;
use App\Form\TransactionType;
use App\Model\Session;
use App\Service\ErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends AbstractController
{
    /**
     * Get the view of an account
     * @param Request $request
     * @param $id
     * @param $navigation
     * @return Response
     * @throws \Exception
     */
    public function view(Request $request, $id, $navigation)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $accountRepo = $em->getRepository('App:Account');
        $session = $request->getSession();

        // Get favorite account if no ID is defined
        $account = 0 === intval($id) ?
            $accountRepo->findUserFavorite($user->getId()) : $accountRepo->findByUserById($user->getId(), $id);
        if(null === $account) {
            throw new \Exception("Account not found");
        }

        // Reinitialize date when the account is changed
        if($session->get(Session::ID_ACCOUNT) !== $account->getId()) {
            $session->set(Session::NAVIGATION_ACCOUNT, null);
        }

        // Get date asked
        $activeDate = $session->get(Session::NAVIGATION_ACCOUNT);
        if(null === $activeDate) {
            $activeDate = new \DateTime();
        }
        else if('previous' === $navigation) {
            $activeDate = $activeDate->modify('-1 month');
        }
        else if('next' === $navigation) {
            $activeDate = $activeDate->modify('+1 month');
        }

        // Get transactions
        $transactions = $em->getRepository('App:Transaction')->findByAccount($account->getId(), $activeDate);

        // Add a new transaction
        $newTransaction = new Transaction();
        $newTransaction->setIdAccount($account);
        $transactionForm = $this->createForm(TransactionType::class, $newTransaction);
        if($request->isMethod('post'))
        {
            $transactionForm->handleRequest($request);
            if($transactionForm->isSubmitted() && $transactionForm->isValid())
            {
                $em->persist($newTransaction);
                $em->flush($newTransaction);

                return $this->redirectToRoute($request->get('_route', ['id' => $account->getId(),]));
            }
        }

        // Save the account ID and the date in session
        $session->set(Session::ID_ACCOUNT, $account->getId());
        $session->set(Session::NAVIGATION_ACCOUNT, $activeDate);

        return $this->render('account/view.html.twig', [
            'account' => $account,
            'transactions' => $transactions,
            'actualDate' => $activeDate,
            'transactionForm' => $transactionForm->createView(),
        ]);
    }

    /**
     * Create an account of user
     * @param Request $request
     * @param ErrorHandler $errorHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function add(Request $request, ErrorHandler $errorHandler)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('App:Account');
        $session = $request->getSession();

        $account = new Account();
        $account->setIdUsersite($user);
        $form = $this->createForm(AccountType::class, $account);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->get('labelName')->getData();
            $newAccount = $repo->createFromUser($data, $user->getId());

            if(null === $newAccount) {
                return $this->redirectToRoute('account.list');
            }

            return $this->redirectToRoute('account.details', array(
                'id' => $newAccount->getId(),
            ));
        }

        $session->set(Session::ERROR_ADD_ACCOUNT, $errorHandler->handleFormErrors($form->getErrors(true)));
        return $this->redirectToRoute('account.list');
    }
}