<?php

namespace App\Form;

use App\Form\Models\CommutatorFormModel;
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
            ->add('ip')
            ->add('gateway')
            ->add('portsAmount')
            ->add('physicalAddress')
            ->add('physicalSerial')
            ->add('state', StateEnumType::class)
            ->add('brand')
            ->add('model')
            ->add('inventory')
            ->add('contic')
            ->add('address', AddressType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommutatorFormModel::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }

}
