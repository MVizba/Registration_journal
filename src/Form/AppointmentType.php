<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Client;
use App\Entity\Patient;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        unset($options);
        $builder
            ->add('date', DateTimeType::class, [
                'label' => 'Data',
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'required' => false,
            ])
            ->add('registrationDate', DateTimeType::class, [
                'label' => 'Registracijos data',
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'required' => false,
            ])
            ->add('symptomsDate', DateTimeType::class, [
                'label' => 'Simptomų data',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => new \DateTime('now'),
                        'message' => 'Simptomų data negali būti vėlesnė už dabartinę datą',
                    ]),
                ],
            ])
            ->add('status', TextareaType::class, [
                'label' => 'Statusas',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Prašome nurodyti statą.',
                    ]),
                ],
            ])
            ->add('diagnosis', TextareaType::class, [
                'label' => 'Diagnozė',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Prašome nurodyti diagnozę.',
                    ]),
                ],
            ])
            ->add('services', TextareaType::class, [
                'label' => 'Paslaugos',
                'required' => false,
            ])
            ->add('endResult', TextareaType::class, [
                'label' => 'Rezultatas',
                'required' => false,
            ])
            ->add('client', EntityType::class, [
                'label' => 'Klientas',
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return $client->getName().' '.$client->getLastName();
                },
                'placeholder' => 'Pasirinkite Klientą',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Pasirinkite sąvininką prieš pildant formą.',
                    ]),
                ],
            ])
            ->add('patient', EntityType::class, [
                'label' => 'Augintinis',
                'class' => Patient::class,
                'choice_label' => 'name',
                'placeholder' => 'Pasirinkite Augintinį',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Pasirinkite augintinį.',
                    ]),
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'group_by' => function ($patient) {
                    return $patient->getClient()->getName().' '.$patient->getClient()->getLastName();
                },
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
