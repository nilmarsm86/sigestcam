<?php

namespace App\Form;

use App\Entity\StructuredCable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StructuredCableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('physicalAddress', null, [
                'label' => 'Dirección física:',
            ])
            ->add('point', null, [
                'label' => 'Punto de enlace:',
            ])
            ->add('path', TextareaType::class, [
                'label' => 'Ruta:',
            ])
            ->add('feederCable', null, [
                'label' => 'Cable alimentador:',
            ])
            ->add('pair', null, [
                'label' => 'Par:',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StructuredCable::class,
        ]);
    }
}
