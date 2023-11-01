<?php

namespace App\Form;

use App\Entity\Camera;
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

class CameraType extends AbstractType
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
            ->add('physicalAddress', TextareaType::class, [
                'label' => 'Dirección física:',
                'constraints' => $physicalAddress
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
                'crud' => $options['crud']
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
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

    /**
     * @param FormEvent $event
     * @param array $options
     * @return void
     */
    private function onPreSetData(FormEvent $event, array $options)
    {
        $camera = $event->getData();
        $form = $event->getForm();

        $constraintIpEquipment = new IpEquipment();
        $constraintIpEquipment->equipment = $camera;

        $ipConstrains = [$constraintIpEquipment];
        if($options['crud'] === false){
            array_push(
                $ipConstrains,
                new NotBlank(message: 'Establezca el IP de la cámara.'),
                new Ip(message:'Establezca un IP válido.')
            );
        }

        $form->add('ip', null, [
            'label' => 'IP:',
            'constraints' => $ipConstrains
        ]);

        if ($camera && null !== $camera->getId()) {
            $form->add('observation', TextareaType::class, [
                'label' => 'Observaciones:',
            ]);
        }
    }
}
