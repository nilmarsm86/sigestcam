<?php

namespace App\Form\Types;

use App\DTO\AddressForm;
use App\Entity\Municipality;
use App\Entity\Province;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('province', EntityType::class, [
                'class' => Province::class,
                'attr' => [
                    'data-model' => 'query'
                ],
                'placeholder' => '-Seleccione-',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddressForm::class,
        ]);
    }

    public function onPreSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $form->add('municipality', EntityType::class, [
            'class' => Municipality::class,
            'placeholder' => null === $data?->province ? '-Seleccione provincia-' : '-Seleccione-',
            'choices' => (null === $data?->province) ? [] : $data?->province?->getMunicipalities(),
            'disabled' => (null === $data?->province || $data?->province?->getMunicipalities()->count() === 0),
        ]);
    }

}
