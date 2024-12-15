<?php

namespace App\Form;

use App\Entity\DrugWarehouse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
