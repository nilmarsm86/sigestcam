<?php

namespace App\Form;

use App\Entity\Camera;
use App\Form\Types\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CameraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ip', null, [
                'label' => 'IP:',
            ])
            ->add('physicalAddress', TextareaType::class, [
                'label' => 'Dirección física:',
            ])
            ->add('physicalSerial', null, [
                'label' => 'Número de serie:',
            ])
            ->add('electronicSerial', null, [
                'label' => 'Número de serie electrónico:',
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
            ->add('user', null, [
                'label' => 'Usuario:',
            ])
            ->add('password', null, [
                'label' => 'Contraseña:',
            ]);

        if($options['crud']){
            $builder->add('address', AddressType::class, [
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Camera::class,
            'attr' => [
                'novalidate' => true,
                'class' => 'camera'
            ],
            'province' => 0,
            'municipality' => 0,
            'crud' => false
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('crud', 'bool');
    }
}
