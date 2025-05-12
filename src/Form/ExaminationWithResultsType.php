<?php

namespace App\Form;

use App\Entity\Examination;
use App\Entity\ExaminationWithResults;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @SuppressWarnings("Unused")
 */
class ExaminationWithResultsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'required' => false,
            ])
            ->add('examination', EntityType::class, [
                'class' => Examination::class,
                'choice_label' => 'examinationName',
            ])

            ->add('result', null, ['label' => 'Rezultatas'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExaminationWithResults::class,
        ]);
    }
}
