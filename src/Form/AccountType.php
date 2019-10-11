<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('labelName', TextType::class, array(
                'attr' => array(
                    'maxlength' => 50,
                ),
                'constraints' => array(
                    new Regex([
                        'pattern' => '/[a-zA-Z0-9\s]+/',
                        'message' => 'account.label_name.regex',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'account.label_name.max_length',
                    ]),
                ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
