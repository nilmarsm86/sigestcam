<?php

namespace App\Form;

use App\Entity\Modem;
use App\Form\Types\AddressType;
use App\Validator\IpEquipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $physicalAddress = [];
        if($options['crud'] === false && $options['slave'] === false){
            $physicalAddress = [
                new NotBlank(message: 'La dirección física no debe estar vacía.'),
            ];
        }

        $builder
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
            ])
        ;

        if($options['crud']){
            $builder->add('address', AddressType::class, [
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'crud' => $options['crud']
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Modem::class,
            'attr' => [
                'novalidate' => true,
                'class' => 'camera'
            ],
            'province' => 0,
            'municipality' => 0,
            'crud' => false,
            'slave' => false,
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('crud', 'bool');
        $resolver->setAllowedTypes('slave', 'bool');
    }

    private function onPreSetData(FormEvent $event)
    {
        $modem = $event->getData();
        $form = $event->getForm();

        $constraintIpEquipment = new IpEquipment();
        $constraintIpEquipment->equipment = $modem;

        $ipConstrains = [$constraintIpEquipment];

        $form->add('ip', null, [
            'label' => 'IP:',
            'constraints' => $ipConstrains
        ]);

        if ($modem && null !== $modem->getId()) {
            $form->add('observation', TextareaType::class, [
                'label' => 'Observaciones:',
            ]);
        }
    }
}
