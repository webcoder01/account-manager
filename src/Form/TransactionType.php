<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Form\Type\BulmaSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Model\Session;
use App\Service\DateManager;
use App\Form\Type\RefTransactionType;

class TransactionType extends AbstractType
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
            ->add('actionDate', ChoiceType::class, [
                'choices' => DateManager::getDayOptions($this->dateAccount),
                'label' => 'Date',
            ])
            ->add('idRefTransactionType', RefTransactionType::class, [
                'label' => 'Type',
            ])
            ->add('save', BulmaSubmitType::class, [
                'additional_button' => $options['additional_button'] ? BulmaSubmitType::getNewButton('Annuler', 'is-text', $newButtonRoute) : [],
                'label' => 'Enregistrer',
                'attr' => ['class' => BulmaSubmitType::addClass('is-primary')],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'additional_button' => true,
        ]);
    }
}
