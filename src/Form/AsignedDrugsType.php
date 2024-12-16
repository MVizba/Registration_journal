<?php

namespace App\Form;

use App\Entity\AsignedDrugs;
use App\Entity\DrugWarehouse;
use App\Repository\DrugWarehouseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AsignedDrugsType extends AbstractType
{
    private $drugWarehouseRepository;

    public function __construct(DrugWarehouseRepository $drugWarehouseRepository)
    {
        $this->drugWarehouseRepository = $drugWarehouseRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $availableDrugs = $this->drugWarehouseRepository->findDrugsWithAvailableStock();
        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'required' => true,
            ])
            ->add('amount')
            ->add('drugWarehouse', EntityType::class, [
                'class' => DrugWarehouse::class,
                'placeholder' => 'Choose a drug',
                'choice_label' => function (DrugWarehouse $drugWarehouse) {
                    return $drugWarehouse->getDrugName().' ('.$drugWarehouse->getType().' - Available: '.$drugWarehouse->getAmount().')';
                },
                'choices' => $availableDrugs,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AsignedDrugs::class,
        ]);
    }
}
