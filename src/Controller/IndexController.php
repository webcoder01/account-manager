<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function index()
    {
        $now = new \DateTime();
        return $this->redirectToRoute('account.view', [
            'year' => $now->format('Y'),
            'month' => $now->format('n'),
        ]);
    }
}
