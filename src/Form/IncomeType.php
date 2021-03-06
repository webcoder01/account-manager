<?php

namespace App\Form;

use App\Entity\Income;
use App\Form\Type\BulmaSubmitType;
use App\Model\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class IncomeType extends AbstractType
{
    private $dateAccount;
    private $router;

    public function __construct(SessionInterface $session, RouterInterface $router)
    {
        $this->dateAccount = $session->get(Session::NAVIGATION_ACCOUNT, new \DateTime());
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $newButtonRoute = $this->router->generate('account.view', ['year' => $this->dateAccount->format('Y'), 'month' => $this->dateAccount->format('n')]);

        $builder
            ->add('labelName', TextType::class, [
                'label' => 'Libellé',
                'attr' =>[
                    'maxlength' => 100,
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/[a-zA-Z0-9\s]+/',
                        'message' => 'Le libellé ne peut contenir de caractères spéciaux',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom de l\'opération est limité à 100 caractères',
                    ]),
                ],
            ])
            ->add('amount', TextType::class, [
                'label' => 'Montant',
                'attr' => [
                    'maxlength' => 9,
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^([0-9]{1,6}|[0-9]{1,6}\.[0-9]{1,2})$/',
                        'message' => 'Le montant ne peut dépasser 999 999 € et ne peut comporter que des chiffres et un . comme séparateur',
                    ]),
                ],
            ])
            ->add('save', BulmaSubmitType::class, [
                'attr' => ['class' => BulmaSubmitType::addClass('is-primary')],
                'label' => 'Enregistrer',
                'additional_button' => $options['additional_button'] ? BulmaSubmitType::getNewButton('Annuler', 'is-text', $newButtonRoute) : [],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Income::class,
            'additional_button' => true,
        ]);
    }
}
