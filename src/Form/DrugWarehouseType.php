<?php

namespace App\Form;

use App\Entity\DrugWarehouse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class DrugWarehouseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateOfReceipt', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('DrugName', null, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('drugManufacturer', null, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('documentNumber', null, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('amount', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'The amount must be 0 or greater.',
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'ml' => 'ml',
                    'g' => 'g',
                    'unit' => 'unit',
                    'bottle' => 'bottle',
                    'pill' => 'pill',
                ],
                'placeholder' => 'Choose a type',
                'required' => true,
            ])
            ->add('manufactureDate', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'The date can not be in the future',
                    ]),
                ],
                'required' => true,
            ])
            ->add('expirationDate', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'The expiration date must be today or in the future.',
                    ]),
                ],
                'required' => true,
            ])
            ->add('series', null, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('whereObtainedFrom', null, [
                'constraints' => [new Assert\NotBlank()],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DrugWarehouse::class,
        ]);
    }
}
