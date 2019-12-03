<?php

namespace App\Form;

use App\Entity\Budget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use App\Form\Type\RefTransactionType;

class BudgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('labelName', TextType::class, [
                'attr' => [
                    'class' => 'input',
                    'maxlength' => 100,
                ],
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom de l\'opération est limité à 100 caractères',
                    ]),
                ],
            ])
            ->add('amount', TextType::class, [
                'attr' => [
                    'class' => 'input',
                    'maxlength' => 9,
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/([0-9]{1,6}|[0-9]{1,6}\.[0-9]{1,2})/',
                        'message' => 'Le montant ne peut dépasser 999 999 € et ne peut comporter que des chiffres et un . comme séparateur',
                    ]),
                ],
            ])
            ->add('idRefTransactionType', RefTransactionType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Budget::class,
        ]);
    }
}
