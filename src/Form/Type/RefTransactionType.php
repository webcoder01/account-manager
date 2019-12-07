<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class RefTransactionType extends AbstractType
{    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => 'App:RefTransactionType',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('t')
                    ->addSelect('c')
                    ->innerJoin('t.idRefTransactionCategory', 'c')
                    ->orderBy('c.labelName', 'ASC')
                    ->addOrderBy('t.labelName', 'ASC');
            },
            'choice_label' => 'labelName',
            'group_by' => function($choice, $key, $value) {
                return $choice->getIdRefTransactionCategory()->getLabelName();
            }
        ]);
    }
    
    public function getParent()
    {
        return EntityType::class;
    }
}
