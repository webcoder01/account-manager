<?php

namespace App\Form;

use App\Entity\Usersite;
use App\Form\Type\BulmaSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class LoginType extends AbstractType
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emailCanonical', TextType::class, array(
                'attr' => array(
                    'class' => 'login-input',
                ),
                'required' => true,
                'label' => 'Email',
            ))
            ->add('password', PasswordType::class, array(
                'required' => true,
                'label' => 'Mot de passe',
            ))
            ->add('save', BulmaSubmitType::class, [
                'attr' => ['class' => BulmaSubmitType::addClass('is-primary is-medium')],
                'label' => 'Connexion',
                'is_centered' => true,
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
