<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Client;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AppointmentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'required' => false,
            ])
            ->add('registrationDate', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'required' => false,
            ])
            ->add('symptomsDate', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => new \DateTime('now'),
                        'message' => 'The date of symptoms cannot be greater than Now',
                    ]),
                ],
            ])
            ->add('status', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please provide the status.',
                    ]),
                ],
            ])
            ->add('diagnosis', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please provide the diagnosis.',
                    ]),
                ],
            ])
            ->add('services', TextareaType::class, [
                'required' => false,
            ])
            ->add('endResult', TextareaType::class, [
                'required' => false,
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return $client->getName().' '.$client->getLastName();
                },
                'placeholder' => 'Select an owner',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please select an owner before filling the form.',
                    ]),
                ],
            ])
            ->add('patient', EntityType::class, [
                'class' => Patient::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a patient',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please select a patient.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
