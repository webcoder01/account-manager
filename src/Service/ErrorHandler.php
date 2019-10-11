<?php


namespace App\Service;


use Symfony\Component\Form\FormErrorIterator;

class ErrorHandler
{
    /**
     * Return form errors in a simple array
     * @param FormErrorIterator $iterator
     * @return array
     */
    public function handleFormErrors(FormErrorIterator $iterator): array
    {
        $errors = [];

        foreach ($iterator as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }

        return $errors;
    }
}