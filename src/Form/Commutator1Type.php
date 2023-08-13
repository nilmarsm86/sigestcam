<?php

namespace App\Form;

use App\Entity\Commutator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Commutator1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand')
            ->add('ip')
            ->add('physicalAddress')
            ->add('physicalSerial')
            ->add('model')
            ->add('inventory')
            ->add('contic')
            ->add('state')
            ->add('portsAmount')
            ->add('gateway')
            ->add('multicast')
            ->add('municipality')
            ->add('port')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commutator::class,
        ]);
    }
}
