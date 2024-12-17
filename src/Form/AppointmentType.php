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
            ->add('status')
            ->add('diagnosis')
            ->add('services')
            ->add('endResult')
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('patient', EntityType::class, [
                'class' => Patient::class,
                'choice_label' => 'name',
                'required' => true,
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
