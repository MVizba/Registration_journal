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
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateOfReceipt', DateTimeType::class, [
                'label' => 'Gavimo data',
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('DrugName', null, [
                'label' => 'Pavadinimas',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('drugManufacturer', null, [
                'label' => 'Gamintojas',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('documentNumber', null, [
                'label' => 'Sąskaitos numeris',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('amount', null, [
                'label' => 'Kiekis',
                'constraints' => [
                    new Assert\NotBlank(),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Kiekis turi būti didesnis už 0.',
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Tipas',
                'choices' => [
                    'ml' => 'ml',
                    'g' => 'g',
                    'unit' => 'unit',
                    'bottle' => 'bottle',
                    'pill' => 'pill',
                ],
                'placeholder' => 'Pasirinkite tipą',
                'required' => true,
            ])
            ->add('manufactureDate', DateType::class, [
                'label' => 'Pagaminimo data',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'Data negali būti ateityje',
                    ]),
                ],
                'required' => true,
            ])
            ->add('expirationDate', DateType::class, [
                'label' => 'Galioja iki:',
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'Galiojimo data turi būti šiandien arba vėliau.',
                    ]),
                ],
                'required' => true,
            ])
            ->add('series', null, [
                'label' => 'Serija',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('whereObtainedFrom', null, [
                'label' => 'Tiekėjas',
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
