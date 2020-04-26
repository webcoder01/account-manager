<?php


namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType;
use App\Form\TransactionType;
use App\Model\Session;
use App\Service\ErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\DateManager;
use App\Form\IncomeType;
use App\Model\OperationData;
use App\Form\BudgetType;
use App\Service\BudgetManager;

class AccountController extends AbstractController
{
    /**
     * Get the view of an account
     *
     * @param Request $request
     * @param $id
     * @param $year
     * @param $month
     *
     * @return Response
     * @throws \Exception
     */
    public function view(Request $request, BudgetManager $budgetManager, $id, $year, $month)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $accountRepo = $em->getRepository('App:Account');
        $transactionRepo = $em->getRepository('App:Transaction');
        $incomeRepo = $em->getRepository('App:Income');
        $budgetRepo = $em->getRepository('App:Budget');
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
        
        // Get amount left
        $amountLeft = $accountRepo->getAmountLeftFromAccount($account->getId(), $dateAsked);
        
        // Save the account ID and the date in session
        $session->set(Session::ID_ACCOUNT, $account->getId());
        $session->set(Session::NAVIGATION_ACCOUNT, $dateAsked);

        // Get transactions
        $transactions = $transactionRepo->findByAccount($account->getId(), $dateAsked);
        $totalAmount = $transactionRepo->getTotalAmountByAccountByMonth($account->getId(), $dateAsked);
        
        // Get incomes
        $incomes = $incomeRepo->findByAccount($account->getId(), $dateAsked);
        
        // Get budgets
        $budgets = $budgetManager->groupBudgetsByStatus($budgetRepo->findByAccount($account->getId()));

        // Add a new transaction
        $newTransaction = OperationData::createEntity(OperationData::TRANSACTION_TYPE, $account);
        $transactionForm = $this->createForm(TransactionType::class, $newTransaction, ['additional_button' => false]);
        
        // Add a new income
        $newIncome = OperationData::createEntity(OperationData::INCOME_TYPE, $account);
        $newIncome->setActionDate($dateAsked);
        $incomeForm = $this->createForm(IncomeType::class, $newIncome, ['additional_button' => false]);
        
        // Add a new budget
        $newBudget = OperationData::createEntity(OperationData::BUDGET_TYPE, $account);
        $budgetForm = $this->createForm(BudgetType::class, $newBudget, ['additional_button' => false]);
        
        if($request->isMethod('post'))
        {
            $transactionForm->handleRequest($request);
            $incomeForm->handleRequest($request);
            $budgetForm->handleRequest($request);
            
            if(
                    ($transactionForm->isSubmitted() && $transactionForm->isValid()) ||
                    ($incomeForm->isSubmitted() && $incomeForm->isValid()) ||
                    ($budgetForm->isSubmitted() && $budgetForm->isValid())
            )
            {
                $entity = $transactionForm->isSubmitted() ? $newTransaction : ($incomeForm->isSubmitted() ? $newIncome : $newBudget);
                $em->persist($entity);
                $em->flush();

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
            'budgets' => $budgets,
            'totalAmount' => $totalAmount,
            'amountLeft' => $amountLeft,
            'date' => $dateAsked,
            'transactionForm' => $transactionForm->createView(),
            'incomeForm' => $incomeForm->createView(),
            'budgetForm' => $budgetForm->createView(),
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