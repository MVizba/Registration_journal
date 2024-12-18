<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ClientType extends AbstractType
{
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
            ->add('address', TextType::class, [
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
            ->add('phone', TextType::class, [
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
                ],
            ]);
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
