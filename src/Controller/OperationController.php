<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TransactionType;
use App\Form\IncomeType;
use App\Model\Session;
use Symfony\Component\HttpFoundation\Response;
use App\Model\OperationData;
use App\Service\AccountManager;

class OperationController extends AbstractController
{  
    /**
     * Add a transaction or an income
     * @param Request $request
     * @param string $type
     * @return Response
     */
    public function add(Request $request, AccountManager $accountManager, $type)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $account = $accountManager->getAccountFromSession();
        if(null === $account) {
            throw new \Exception('Account not found');
        }
        
        $entity = OperationData::createEntity($type, $account);
        $formType = OperationData::TRANSACTION_TYPE === $type ? TransactionType::class : IncomeType::class;
        $form = $this->createForm($formType, $entity);
        
        if($request->isMethod('post'))
        {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->persist($entity);
                $em->flush();
                $date = $session->get(Session::NAVIGATION_ACCOUNT);
                
                return $this->redirectToRoute('account.view', [
                    'year' => $date->format('Y'),
                    'month' => $date->format('n'),
                    'id' => $entity->getIdAccount()->getId(),
                ]);
            }
        }
        
        return $this->render('operation/operation.form.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * Edit a transaction or an income
     * @param Request $request
     * @param string $type
     * @param string $id
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, $type, $id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $repository = OperationData::TRANSACTION_TYPE === $type ? $em->getRepository('App:Transaction') : $em->getRepository('App:Income');
        $formType = OperationData::TRANSACTION_TYPE === $type ? TransactionType::class : IncomeType::class;
        $entity = $repository->findByUserById($user->getId(), $id);
        if(null === $entity) {
            throw new \Exception($type . ' not found');
        }
        
        $session->set(Session::OPERATION_EDIT, ['type' => $type, 'id' => intval($id),]);
        $form = $this->createForm($formType, $entity);
        if($request->isMethod('post'))
        {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em->flush();
                $date = $session->get(Session::NAVIGATION_ACCOUNT);
                
                return $this->redirectToRoute('account.view', [
                    'year' => $date->format('Y'),
                    'month' => $date->format('n'),
                    'id' => $entity->getIdAccount()->getId(),
                ]);
            }
        }
        
        return $this->render('operation/operation.form.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
            'operationId' => $id,
        ]);
    }
    
    /**
     * Delete a transaction or an income
     * @param Request $request
     * @param string $type
     * @param string $id
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request, $type, $id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $operationSession = $session->get(Session::OPERATION_EDIT);
        $repository = OperationData::TRANSACTION_TYPE === $type ? $em->getRepository('App:Transaction') : $em->getRepository('App:Income');
        $entity = $repository->findByUserById($user->getId(), $id);
        if(null === $entity) {
            throw new \Exception($type . ' not found');
        }
        
        if($operationSession['type'] !== $type || $operationSession['id'] !== intval($id)) {
            throw new \Exception('Delete of ' . $type . ' not allowed');
        }
        
        $em->remove($entity);
        $em->flush();
        $date = $session->get(Session::NAVIGATION_ACCOUNT);
        
        return $this->redirectToRoute('account.view', [
            'year' => $date->format('Y'),
            'month' => $date->format('n'),
            'id' => $entity->getIdAccount()->getId(),
        ]);
    }
}
