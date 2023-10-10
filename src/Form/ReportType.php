<?php

namespace App\Form;

use App\Entity\Report;
use App\Form\DataTransformer\EquipmentToIdTransformer;
use App\Form\Types\AimEnumType;
use App\Form\Types\InterruptionReasonEnumType;
use App\Form\Types\PriorityEnumType;
use App\Form\Types\ReportTypeEnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function __construct(private EquipmentToIdTransformer $equipmentToIdTransformer) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('number')
            //->add('specialty')
            //->add('entryDate')
            //->add('closeDate')
            ->add('type', ReportTypeEnumType::class, [
                'label' => 'Tipo:',
            ])
            ->add('interruption', InterruptionReasonEnumType::class, [
                'mapped' => false,
                'label' => 'Interrupción:',
                'attr' => [
                    'data-action' => 'connection-list-table#selectReason'
                ]
            ])
            ->add('interruptionReason', null, [
                'label' => 'Razón de interrupción:',
                'row_attr' => ['style' => 'display:none'],
            ])
            ->add('priority', PriorityEnumType::class, [
                'label' => 'Prioridad:'
            ])
            ->add('flaw', null, [
                'label' => 'Motivo de interrupción:'
            ])
            //->add('observation')
            //->add('solution')
            //->add('unit')
            //->add('state')
            ->add('aim', AimEnumType::class, [
                'label' => 'Aim:'
            ])
            ->add('equipment', HiddenType::class, [
                'data' => $options['equipment'],
            ])
            //->add('boss')
            //->add('managementOfficer')
            //->add('organ')
        ;

        $builder->get('equipment')->addModelTransformer($this->equipmentToIdTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
            'equipment' => 0,
            'attr' => [
                'novalidate' => true,
            ],
        ]);

        $resolver->setAllowedTypes('equipment', 'int');
    }
}
