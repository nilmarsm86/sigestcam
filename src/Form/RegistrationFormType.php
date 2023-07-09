<?php

namespace App\Form;

use App\DTO\RegistrationForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control form-control-user no-border-left',
                    'placeholder' => 'Nombres'
                ]
            ])
            ->add('lastname', null, [
                'attr' => [
                    'class' => 'form-control form-control-user no-border-left',
                    'placeholder' => 'Apellidos'
                ]
            ])
            ->add('username', null, [
                'attr' => [
                    'class' => 'form-control form-control-user no-border-left',
                    'placeholder' => 'Usuario',
                    'aria-describedby' => 'usernameHelp'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class)

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Contraseña',
                        'autocomplete' => 'new-password',
                        'class' => 'form-control form-control-user no-border-left',
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Repetir Contraseña',
                        'autocomplete' => 'new-password',
                        'class' => 'form-control form-control-user no-border-left',
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationForm::class,
            'attr' => [
                'class' => 'user register',
                //'novalidate' => 'novalidate'
            ]
        ]);
    }
}
