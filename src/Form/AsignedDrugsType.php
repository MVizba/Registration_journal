<?php

namespace App\Form;

use App\Entity\AsignedDrugs;
use App\Entity\DrugWarehouse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AsignedDrugsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'ml' => 'ml',
                    'g' => 'g',
                    'unit' => 'unit',
                    'bottle' => 'bottle',
                ],
                'placeholder' => 'Choose a type',
                'required' => true,
            ])
            ->add('amount')
            ->add('drugWarehouse', EntityType::class, [
                'class' => DrugWarehouse::class,
                'placeholder' => 'Choose a drug',
                'choice_label' => function (DrugWarehouse $drugWarehouse) {
                    return $drugWarehouse->getDrugName();
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AsignedDrugs::class,
        ]);
    }
}
