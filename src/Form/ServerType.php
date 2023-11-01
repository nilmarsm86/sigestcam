<?php

namespace App\Form;

use App\Entity\Server;
use App\Form\Types\AddressType;
use App\Validator\IpEquipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                'crud' => $options['crud']
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Server::class,
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
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
        $server = $event->getData();
        $form = $event->getForm();

        $constraintIpEquipment = new IpEquipment();
        $constraintIpEquipment->equipment = $server;

        $ipConstrains = [$constraintIpEquipment];

        $form->add('ip', null, [
            'label' => 'IP:',
            'constraints' => $ipConstrains
        ]);

        if ($server && null !== $server->getId()) {
            $form->add('observation', TextareaType::class, [
                'label' => 'Observaciones:',
            ]);
        }
    }
}
