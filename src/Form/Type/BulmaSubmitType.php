<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\SubmitButtonTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BulmaSubmitType extends AbstractType implements SubmitButtonTypeInterface
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['additional_button'] = $options['additional_button'];
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'additional_button' => [],
        ]);
    }

    public function getParent()
    {
        return SubmitType::class;
    }

    /**
     * <p>Get an array to add an new button next to the submit one</p>
     * <p>Currently, it is possible to add only one more</p>
     *
     * @param string $label
     * @param string $class
     * @param string|null $route
     *
     * @return array
     */
    public static function getNewButton(string $label, string $class = '', string $route = null): array
    {
        return [
            'label' => $label,
            'class' => $class,
            'type' => (null !== $route ? 'a' : 'button'),
            'link' => $route,
        ];
    }

    /**
     * Add class to the prefix
     *
     * @param string $class
     *
     * @return string
     */
    public static function addClass(string $class): string
    {
        return 'button ' . $class;
    }
}