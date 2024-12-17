<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('client', EntityType::class, [
            'class' => Client::class,
            'choice_label' => function (Client $client) {
                return $client->getName().' '.$client->getLastName();
            },
            'placeholder' => 'Select an owner',
            'required' => true,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please select an owner before filling the form.',
                ]),
            ],
        ]);

        //  PRE_SET_DATA
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            // ar klientas nustatytas formoj?
            $clientSelected = $data instanceof Patient && null !== $data->getClient();

            // fileds priklausomai nuo cliento
            $this->addFields($form, !$clientSelected);
        });

        // Form event listener PRE_SUBMIT
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (is_array($data) && isset($data['client'])) {
                $clientSelected = !empty($data['client']);
            } else {
                $clientSelected = false;
            }

            $this->addFields($form, !$clientSelected);
        });
    }

    private function addFields(FormInterface $form, bool $disabled): void
    {
        $form
            ->add('name', null, [
                'disabled' => $disabled,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Name is required.']),
                ],
            ])
            ->add('type', null, ['disabled' => $disabled])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Male' => 'male',
                    'Female' => 'female',
                    'Other' => 'other',
                ],
                'placeholder' => 'Choose a gender',
                'disabled' => $disabled,
                'required' => true,
            ])
            ->add('age', null, [
                'widget' => 'single_text',
                'disabled' => $disabled,
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'The date of birth cannot be in the future.',
                    ]),
                ],
            ])
            ->add('markingNumber', null, ['disabled' => $disabled])
            ->add('passportNumber', null, ['disabled' => $disabled])
            ->add('appearance', null, ['disabled' => $disabled]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
