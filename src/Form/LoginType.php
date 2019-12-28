<?php

namespace App\Form;

use App\Entity\Usersite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emailCanonical', TextType::class, array(
                'attr' => array(
                    'class' => 'input login-input',
                ),
                'required' => true,
            ))
            ->add('password', PasswordType::class, array(
                'attr' => array(
                    'class' => 'input',
                ),
                'required' => true,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usersite::class,
        ]);
    }
}
