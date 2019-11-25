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
use App\Service\DateManager;
use App\Entity\Income;
use App\Form\IncomeType;
use App\Model\OperationData;

class AccountController extends AbstractController
{
    /**
     * Get the view of an account
     * @param Request $request
     * @param $id
     * @param $year
     * @param $month
     * @return Response
     * @throws \Exception
     */
    public function view(Request $request, $id, $year, $month)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $accountRepo = $em->getRepository('App:Account');
        $transactionRepo = $em->getRepository('App:Transaction');
        $incomeRepo = $em->getRepository('App:Income');
        $session = $request->getSession();

        // Get favorite account if no ID is defined
        $account = 0 === intval($id) ?
            $accountRepo->findUserFavorite($user->getId()) : $accountRepo->findByUserById($user->getId(), $id);
        if(null === $account) {
            throw new \Exception("Account not found");
        }

        // Get date asked
        $dateString = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $dateAsked = \DateTime::createFromFormat('Y-m-d', $dateString);
        
        // Save the account ID and the date in session
        $session->set(Session::ID_ACCOUNT, $account->getId());
        $session->set(Session::NAVIGATION_ACCOUNT, $dateAsked);

        // Get transactions
        $transactions = $transactionRepo->findByAccount($account->getId(), $dateAsked);
        $totalAmount = $transactionRepo->getTotalAmountByAccountByMonth($account->getId(), $dateAsked);
        $amountLeft = $accountRepo->getAmountLeftFromAccount($account->getId(), $dateAsked);
        
        // Get incomes
        $incomes = $incomeRepo->findByAccount($account->getId(), $dateAsked);

        // Add a new transaction
        $newTransaction = OperationData::createEntity(OperationData::TRANSACTION_TYPE, $account);
        $transactionForm = $this->createForm(TransactionType::class, $newTransaction);
        
        // Add a new income
        $newIncome = OperationData::createEntity(OperationData::INCOME_TYPE, $account);;
        $newIncome->setActionDate($dateAsked);
        $incomeForm = $this->createForm(IncomeType::class, $newIncome);
        
        if($request->isMethod('post'))
        {
            $transactionForm->handleRequest($request);
            $incomeForm->handleRequest($request);
            
            if(($transactionForm->isSubmitted() && $transactionForm->isValid()) || ($incomeForm->isSubmitted() && $incomeForm->isValid()))
            {
                $entity = $transactionForm->isSubmitted() ? $newTransaction : $newIncome;
                $em->persist($entity);
                $em->flush($entity);

                return $this->redirectToRoute($request->get('_route'), [
                    'year' => $dateAsked->format('Y'),
                    'month' => $dateAsked->format('n'),
                    'id' => $account->getId(),
                ]);
            }
        }

        return $this->render('account/view.html.twig', [
            'account' => $account,
            'transactions' => $transactions,
            'incomes' => $incomes,
            'totalAmount' => $totalAmount,
            'amountLeft' => $amountLeft,
            'date' => [
                'previous' => DateManager::getPreviousMonthFromDate($dateAsked),
                'now' => $dateAsked,
                'next' => DateManager::getNextMonthFromDate($dateAsked),
            ],
            'transactionForm' => $transactionForm->createView(),
            'incomeForm' => $incomeForm->createView(),
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