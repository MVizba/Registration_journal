<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * @SuppressWarnings("unused")
 */
class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('client', EntityType::class, [
            'label' => 'Savininkas',
            'class' => Client::class,
            'choice_label' => function (Client $client) {
                return $client->getName().' '.$client->getLastName();
            },
            'placeholder' => 'Pasirinkite savininką',
            'required' => true,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Pasirinkite sąvininką prieš pildant formą.',
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

            $clientSelected = false;
            if (is_array($data) && isset($data['client'])) {
                $clientSelected = !empty($data['client']);
            }

            $this->addFields($form, !$clientSelected);
        });
    }

    private function addFields(FormInterface $form, bool $disabled): void
    {
        $form
            ->add('name', null, [
                'label' => 'Vardas',
                'disabled' => $disabled,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Šis laukas privalomas.']),
                ],
                'attr' => [
                    'placeholder' => 'Įrašykite augintinio vardą.',
                ],
            ])
            ->add('type', null, [
                'label' => 'Tipas',
                'disabled' => $disabled,
                'attr' => [
                    'placeholder' => 'Įrašykite augintinio rūšį.',
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Lytis',
                'choices' => [
                    'Patinas' => 'male',
                    'Patelė' => 'female',
                    'Kita' => 'other',
                ],
                'placeholder' => 'Pasirinkite lytį',
                'disabled' => $disabled,
                'required' => true,
            ])
            ->add('age', null, [
                'label' => 'Amžius',
                'widget' => 'single_text',
                'disabled' => $disabled,
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'Gimimo data negali būti ateityje.',
                    ]),
                ],
            ])
            ->add('markingNumber', null, [
                'label' => 'Žymėjimo numeris',
                'disabled' => $disabled,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Šis laukas negali Būti ilgesnis nei {{ limit }} ženklai.',
                    ]),
                ],
                'required' => false,
                'attr' => [
                    'placeholder' => '(neprivaloma)',
                ],
            ])
            ->add('passportNumber', null, [
                'label' => 'Paso numeris',
                'disabled' => $disabled,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Šis laukas negali Būti ilgesnis nei {{ limit }} ženklai.',
                    ]),
                ],
                'required' => false,
                'attr' => [
                    'placeholder' => '(neprivaloma)',
                ],
            ])
            ->add('appearance', TextareaType::class, [
                'label' => 'Išvaizda',
                'disabled' => $disabled,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Šis laukas privalomas.']),
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'Apibūdinkite augintinio išvaizdą',
                    'rows' => 5,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
