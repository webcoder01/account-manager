<?php


namespace App\Controller;


use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Model\Session;
use App\Service\ErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TransactionController extends AbstractController
{
    /**
     * Create a transaction of the account stored in session
     * @param Request $request
     * @param ErrorHandler $errorHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function add(Request $request, ErrorHandler $errorHandler)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $accountId = $request->getSession()->get(Session::ID_ACCOUNT);
        if(null === $accountId) {
            throw new \Exception('An account must be stored in session');
        }

        $account = $em->getRepository('App:Account')->findByUserById($user->getId(), $accountId);
        if(null === $account) {
            throw new \Exception('The transaction cannot be attached to an unknown account');
        }

        $transaction = new Transaction();
        $transaction->setIdAccount($account);
        $form = $this->createForm(TransactionType::class, $transaction);

        if($request->isMethod('post'))
        {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->persist($transaction);
                $em->flush();

                return $this->redirectToRoute('index', ['id' => $accountId,]);
            }
        }

        return $this->render('transaction/form.html.twig', [
            'form' => $form->createView(),
            'errors' => $errorHandler->handleFormErrors($form->getErrors(true)),
        ]);
    }

    /**
     * Edit a transaction given by the id
     * @param Request $request
     * @param $id
     * @param ErrorHandler $errorHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function edit(Request $request, $id, ErrorHandler $errorHandler)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $transaction = $em->getRepository('App:Transaction')->findByUserById($user->getId(), $id);
        if(null === $transaction) {
            throw new \Exception('Transaction not found');
        }

        $request->getSession()->set(Session::ID_TRANSACTION, $transaction->getId());
        $form = $this->createForm(TransactionType::class, $transaction);

        if($request->isMethod('post'))
        {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->flush();

                return $this->redirectToRoute('index', ['id' => $transaction->getIdAccount()->getId(),]);
            }
        }

        return $this->render('transaction/form.html.twig', [
            'form' => $form->createView(),
            'errors' => $errorHandler->handleFormErrors($form->getErrors(true)),
            'isEdit' => true,
        ]);
    }
}