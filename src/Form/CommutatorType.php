<?php

namespace App\Form;

use App\DTO\CommutatorForm;
use App\Form\Types\AddressType;
use App\Form\Types\StateEnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommutatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('physicalAddress')
            ->add('physicalSerial')
            ->add('state', StateEnumType::class)
            ->add('ip')
            ->add('portsAmount')
            ->add('gateway')
            ->add('address', AddressType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommutatorForm::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'csrf_protection' => false
        ]);
    }

}
