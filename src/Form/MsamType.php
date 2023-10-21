<?php

namespace App\Form;

use App\Entity\Msam;
use App\Form\Types\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\NotBlank;

class MsamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ipConstrains = [];
        $gatewayConstrains = [];
        $physicalAddress = [];
        if($options['crud'] === false){
            $ipConstrains = [
                new NotBlank(message: 'Establezca el IP del msam.'),
                new Ip(message:'Establezca un IP válido.')
            ];

            $physicalAddress = [
                new NotBlank(message: 'La dirección física no debe estar vacía.'),
            ];
        }

        $builder
            ->add('ip', null, [
                'label' => 'IP:',
                'constraints' => $ipConstrains
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
            ]);
            //->add('slotAmount');

        if($options['crud']) {
            $builder->add('address', AddressType::class, [
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'crud' => $options['crud']
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $msam = $event->getData();
            $form = $event->getForm();

            if (!$msam || null === $msam->getId()) {
                $form->add('slotAmount', null, [
                    'label' => 'Cantidad de slots de targeta:',
                    'attr' => [
                        'min' => 1,
                        'max' => 20,
                        'list' => 'slots_amount'
                    ]
                ]);
            }
        })
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Msam::class,
            'attr' => [
                'novalidate' => true,
                'class' => 'commutator'
            ],
            'province' => 0,
            'municipality' => 0,
            'crud' => false,
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('crud', 'bool');
    }
}
