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
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The shortcut cannot be empty.',
                    ]),
                    new Assert\Length([
                        'max' => 10,
                        'maxMessage' => 'The shortcut cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('examinationName', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The examination name cannot be empty.',
                    ]),
                    new Assert\Length([
                        'max' => 150,
                        'maxMessage' => 'The examination name cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('norms', null, [
                'constraints' => [
                    new Assert\Length([
                        'max' => 200,
                        'maxMessage' => 'Norms cannot be longer than {{ limit }} characters.',
                    ]),
                ],
                'required' => false,
            ])
            ->add('machine', null, [
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'The machine name cannot be longer than {{ limit }} characters.',
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
