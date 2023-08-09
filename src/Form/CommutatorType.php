<?php

namespace App\Form;

use App\Entity\Commutator;
use App\Form\Types\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommutatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ip', null, [
                'label' => 'IP:',
            ])
            ->add('gateway', null, [
                'label' => 'Gateway:',
            ])
            ->add('portsAmount', null, [
                'label' => 'Cantidad de puertos:',
            ])
            ->add('physicalAddress', null, [
                'label' => 'Dirección física:',
            ])
            ->add('physicalSerial', null, [
                'label' => 'Número de serie:',
            ])
            ->add('brand', null, [
                'label' => 'Marca:',
            ])
            ->add('model', null, [
                'label' => 'Modelo:',
            ])
            ->add('inventory', null, [
                'label' => 'Número de inventario:',
            ])
            ->add('contic', null, [
                'label' => 'Contic:',
            ])
            ->add('address', AddressType::class, [
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commutator::class,
            'attr' => [
                'novalidate' => true,
                'class' => 'commutator'
            ],
            'province' => 0,
            'municipality' => 0,
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
    }

}
