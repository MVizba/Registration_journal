<?php

namespace App\Form;

use App\Entity\AsignedDrugs;
use App\Entity\DrugWarehouse;
use App\Repository\DrugWarehouseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @SuppressWarnings("unused")
 */
class AsignedDrugsType extends AbstractType
{
    private DrugWarehouseRepository $drugWarehouseRepository;

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
                'required' => true,
                'label' => 'Data',
            ])
            ->add('amount', NumberType::class, [
                'required' => true,
                'scale' => 2,
                'attr' => ['min' => 0],
                'label' => 'Kiekis',
            ])
            ->add('drugWarehouse', EntityType::class, [
                'class' => DrugWarehouse::class,
                'placeholder' => 'Pasirinkite vaistus',
                'label' => 'Vaistai',
                'choice_label' => function (DrugWarehouse $drugWarehouse) {
                    return sprintf(
                        '%s (%s.: %s) - Gauta: %s',
                        $drugWarehouse->getDrugName(),
                        $drugWarehouse->getType(),
                        $drugWarehouse->getRemainingAmount(),
                        $drugWarehouse->getDateOfReceipt()->format('Y-m-d')
                    );
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
