<?php

namespace App\Form;

use App\Entity\Report;
use App\Entity\User;
use App\Form\DataTransformer\EquipmentToIdTransformer;
use App\Form\Types\AimEnumType;
use App\Form\Types\InterruptionReasonEnumType;
use App\Form\Types\PriorityEnumType;
use App\Form\Types\ReportTypeEnumType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                    'data-action' => 'report#selectReason'
                ]
            ])
            ->add('equipment', HiddenType::class, [
                'data' => $options['equipment'],
                'constraints' => [
                    new \App\Validator\Report(null, null, null, $options['existReport']),
                ]
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options, $builder): void {
                $report = $event->getData();
                $form = $event->getForm();

                if (!$report || null === $report->getId()) {
                    $form->add('interruptionReason', null, [
                        'label' => 'Razón de interrupción:',
                        'row_attr' => ['style' => 'display:none'],
                    ]);
                }else{
                    $form->add('interruptionReason', null, [
                        'label' => 'Razón de interrupción:',
                    ])
                    ->add('observation', null, [
                        'label' => 'Observaciones:'
                    ])
                    ->add('solution', HiddenType::class, [
//                        'label' => 'Solución:',
//                        'constraints' => [new NotBlank(['message'=>'Para poder cerrar un reporte debe darle solución primero.'])]
                    ]);
                }
            })
            ->add('priority', PriorityEnumType::class, [
                'label' => 'Prioridad:'
            ])
            ->add('flaw', null, [
                'label' => 'Motivo de interrupción:'
            ])

            //->add('unit')
            //->add('state')
            ->add('aim', AimEnumType::class, [
                'label' => 'Función:'
            ])

            ->add('boss', EntityType::class, [
                'label' => 'Jefe:',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->findBosses();
                }
            ])
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
            'existReport' => false,
            'attr' => [
                'novalidate' => true,
                'data-controller' => 'report'
            ],
        ]);

        $resolver->setAllowedTypes('equipment', 'int');
        $resolver->setAllowedTypes('existReport', 'bool');
    }
}
