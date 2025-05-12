<?php

namespace App\Form;

use App\Entity\Examination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @SuppressWarnings("unused")
 */
class ExaminationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shortcut', null, [
                'label' => 'Trumpinys',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Šis laukas negali būti tuščias.',
                    ]),
                    new Assert\Length([
                        'max' => 10,
                        'maxMessage' => 'Šis laukas negali büti ilgesnis nei {{ limit }} ženklų.',
                    ]),
                ],
            ])
            ->add('examinationName', null, [
                'label' => 'Tyrimo pavadinimas',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Šis laukas negali būti tuščias.',
                    ]),
                    new Assert\Length([
                        'max' => 150,
                        'maxMessage' => 'Šis laukas negali büti ilgesnis nei  {{ limit }} ženklų.',
                    ]),
                ],
            ])
            ->add('norms', null, [
                'label' => 'Normos',
                'constraints' => [
                    new Assert\Length([
                        'max' => 200,
                        'maxMessage' => 'Šis laukas negali büti ilgesnis nei {{ limit }} ženklų.',
                    ]),
                ],
                'required' => false,
            ])
            ->add('machine', null, [
                'label' => 'Aparatas',
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Šis laukas negali büti ilgesnis nei {{ limit }} ženklų.',
                    ]),
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Examination::class,
        ]);
    }
}
