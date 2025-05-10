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
                'label' => 'First Name',
                'attr' => ['placeholder' => 'Enter Name'],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Name cannot be empty',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => 'Name must be at least {{ limit }} characters long',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'attr' => ['placeholder' => 'Enter last name'],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Last name cannot be empty',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => 'Last name must be at least {{ limit }} characters long',
                    ]),
                ],
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Address',
                'required' => false,
                'attr' => ['placeholder' => 'Enter address (optional)'],
                'constraints' => [
                    new Assert\Length([
                        'max' => 150,
                        'maxMessage' => 'Address cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone Number',
                'required' => true,
                'attr' => ['placeholder' => 'Enter phone number'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Phone number cannot be empty',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'required' => true,
                'attr' => ['placeholder' => 'Enter email address'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Email cannot be empty',
                    ]),
                    new Assert\Email([
                        'message' => 'Invalid email format',
                    ]),
                    new Callback(function ($email, ExecutionContextInterface $context) use ($options) {
                        $clientRepository = $options['client_repository'] ?? null;
                        $currentClient = $options['data'] ?? null;

                        if (!$clientRepository instanceof \App\Repository\ClientRepository) {
                            throw new \LogicException('The "client_repository" option must be set and must be an instance of ClientRepository.');
                        }

                        $existingClient = $clientRepository->findOneBy(['email' => $email]);
                        if ($existingClient && (!$currentClient || $existingClient->getId() !== $currentClient->getId())) {
                            $context->buildViolation('This email is already registered.')
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
