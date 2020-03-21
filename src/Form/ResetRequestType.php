<?php

namespace App\Form;

use App\Entity\Usersite;
use App\Form\Type\BulmaSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Saisissez votre email',
                'attr' => [
                    'class' => 'input',
                    'maxlength' => 180,
                ],
                'required' => true,
            ])
            ->add('save', BulmaSubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => BulmaSubmitType::addClass('is-primary')],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usersite::class,
        ]);
    }
}
