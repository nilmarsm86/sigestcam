<?php

namespace App\Form;

use App\Entity\Msam;
use App\Form\Types\AddressType;
use App\Validator\IpEquipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MsamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $physicalAddress = [];
        if($options['crud'] === false){
            $physicalAddress = [
                new NotBlank(message: 'La dirección física no debe estar vacía.'),
            ];
        }

        $builder
            ->add('physicalAddress', null, [
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
            $this->onPreSetData($event);
        });
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

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
        $msam = $event->getData();
        $form = $event->getForm();

        $constraintIpEquipment = new IpEquipment();
        $constraintIpEquipment->equipment = $msam;
        $ipConstrains = [$constraintIpEquipment];

        $form->add('ip', null, [
            'label' => 'IP:',
            'constraints' => $ipConstrains
        ]);

        if (!$msam || null === $msam->getId()) {
            $form->add('slotAmount', null, [
                'label' => 'Cantidad de slots de targeta:',
                'attr' => [
                    'min' => 1,
                    'max' => 20,
                    'list' => 'slots_amount'
                ],
                'data' => 1
            ]);
        } else {
            $form->add('observation', TextareaType::class, [
                'label' => 'Observaciones:',
            ]);
        }
    }
}
