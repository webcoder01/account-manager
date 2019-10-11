<?php

namespace App\Form;

use App\Entity\Transaction;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('labelName', TextType::class, [
                'attr' =>[
                    'class' => 'input',
                    'maxlength' => 100,
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/[a-zA-Z0-9\s]+/',
                        'message' => 'transaction.label_name.regex',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'transaction.label_name.max_length',
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
                        'message' => 'transaction.amount.regex',
                    ]),
                ],
            ])
            ->add('actionDate', ChoiceType::class, [
                'choices' => $this->getDayOptions(),
            ])
            ->add('idRefTransactionType', EntityType::class, [
                'class' => 'App:RefTransactionType',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->addSelect('c')
                        ->innerJoin('t.idRefTransactionCategory', 'c')
                        ->orderBy('c.labelName', 'ASC')
                        ->addOrderBy('t.labelName', 'ASC');
                },
                'choice_label' => 'labelName',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }

    private function getDayOptions()
    {
        $now = new \DateTime();
        $days = [];
        $string = $now->format('Y-m');

        for($i = 1; $i <= intval($now->format('t')); $i++) {
            $days[strval($i)] = $string . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        return $days;
    }
}
