<?php

namespace App\Form;

use App\Entity\DrugWarehouse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DrugWarehouseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateOfReceipt', null, [
                'widget' => 'single_text',
            ])
            ->add('DrugName')
            ->add('drugManufacturer')
            ->add('documentNumber')
            ->add('amount')
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
            ->add('manufactureDate', null, [
                'widget' => 'single_text',
            ])
            ->add('expirationDate', null, [
                'widget' => 'single_text',
            ])
            ->add('series')
            ->add('whereObtainedFrom')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DrugWarehouse::class,
        ]);
    }
}
