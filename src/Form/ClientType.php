<?php

namespace App\Form;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ClientType extends AbstractType
{
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Vardas',
                'attr' => ['placeholder' => 'Įrašykite vardą'],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Šis laikas negali būti tuščias',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => 'Šis laukas turi būti bent {{ limit }} ženklu ilgio',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Pavardė',
                'attr' => ['placeholder' => 'Įrašykite pavardę'],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Šis laukas negali būti tuščias',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => 'Šis laukas turi būti bent {{ limit }} ženklų ilgio',
                    ]),
                ],
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Addresas',
                'required' => false,
                'attr' => ['placeholder' => 'Įrašykite adresą (neprivaloma)'],
                'constraints' => [
                    new Assert\Length([
                        'max' => 150,
                        'maxMessage' => 'Šis laukas negali būti ilgesnis nei {{ limit }} ženklai',
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Telefono numeris',
                'required' => true,
                'attr' => ['placeholder' => 'Įrašykite telefono numeri'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Šis laukas negali būti tuščias',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Elektroninis paštas',
                'required' => true,
                'attr' => ['placeholder' => 'Įrašykite elektroninio pašto adresa'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Šis laukas negali būti tuščias',
                    ]),
                    new Assert\Email([
                        'message' => 'Netinkamas elektroninio pašto formatas',
                    ]),
                    new Callback(function ($email, ExecutionContextInterface $context) use ($options) {
                        $clientRepository = $options['client_repository'] ?? null;
                        $currentClient = $options['data'] ?? null;

                        if (!$clientRepository instanceof \App\Repository\ClientRepository) {
                            throw new \LogicException('The "client_repository" option must be set and must be an instance of ClientRepository.');
                        }

                        $existingClient = $clientRepository->findOneBy(['email' => $email]);
                        if ($existingClient && (!$currentClient || $existingClient->getId() !== $currentClient->getId())) {
                            $context->buildViolation('Šis elektroninis paštas jau egzistuoja.')
                                ->addViolation();
                        }
                    }),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'client_repository' => $this->clientRepository,
        ]);
    }
}
