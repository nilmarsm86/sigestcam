<?php

namespace App\Form;

use App\Entity\Commutator;
use App\Form\Types\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommutatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ipConstrains = [];
        $gatewayConstrains = [];
        $physicalAddress = [];
        if($options['crud'] === false){
            $ipConstrains = [
                new NotBlank(message: 'Establezca el IP del switch.'),
                new Ip(message:'Establezca un IP válido.')
            ];

            $physicalAddress = [
                new NotBlank(message: 'La dirección física no debe estar vacía.'),
            ];
        }

        if($options['crud'] === false && $options['slave'] === false){
            $gatewayConstrains = [
                new NotBlank(message: 'El IP gateway no debe estar vacío.'),
                new Ip(message:'Establezca un IP gateway válido.')
            ];
        }

        $builder
            ->add('ip', null, [
                'label' => 'IP:',
                'constraints' => $ipConstrains
            ])
            ->add('gateway', null, [
                'label' => 'Puerta de enlace:',
                'constraints' => $gatewayConstrains
            ])
            ->add('multicast', null, [
                'label' => 'Dirección multicast:',
            ])
            ->add('physicalAddress', TextareaType::class, [
                'label' => 'Dirección física:',
                'constraints' => $physicalAddress
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
        if($options['crud']) {
            $builder->add('address', AddressType::class, [
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'crud' => $options['crud']
            ]);
        }
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $commutator = $event->getData();
                $form = $event->getForm();

                if (!$commutator || null === $commutator->getId()) {
                    $form->add('portsAmount', null, [
                        'label' => 'Cantidad de puertos:',
                        'attr' => [
                            'min' => 1,
                            'max' => 36,
                            'list' => 'ports_amount'
                        ]
                    ]);
                }
            })
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
            'crud' => false,
            'slave' => false,
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('crud', 'bool');
    }

}
