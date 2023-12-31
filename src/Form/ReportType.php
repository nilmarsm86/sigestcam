<?php

namespace App\Form;

use App\Entity\Report;
use App\Entity\User;
use App\Form\DataTransformer\EquipmentToIdTransformer;
use App\Form\DataTransformer\FlawEnumToStringTransformer;
use App\Form\DataTransformer\InterruptionReasonEnumToStringTransformer;
use App\Form\Types\AimEnumType;
use App\Form\Types\FlawEnumType;
use App\Form\Types\InterruptionReasonEnumType;
use App\Form\Types\PriorityEnumType;
use App\Form\Types\ReportTypeEnumType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function __construct(
        private EquipmentToIdTransformer $equipmentToIdTransformer,
        private FlawEnumToStringTransformer $flawEnumToStringTransformer,
        private InterruptionReasonEnumToStringTransformer $interruptionReasonEnumToStringTransformer,
        private Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readOnly = [];
        if($this->security->getUser()->justTechnical()){
            $readOnly = [
                'attr' => [
                    'readonly' => true,
                    //'disabled' => true,
                    'style' => 'background-color: #eaecf4;'
                ]
            ];
        }

        $builder
            ->add('type', ReportTypeEnumType::class, [
                'label' => 'Tipo: ',
            ]+$readOnly)
            ->add('interruptionReason', InterruptionReasonEnumType::class, [
                'label' => 'Motivo de interrupción: ',
                'attr' => [
                ]+['readonly' => $this->security->getUser()->justTechnical(),'disabled' => $this->security->getUser()->justTechnical()]
            ])
            ->add('equipment', HiddenType::class, [
                'data' => $options['equipment'],
                'constraints' => [
                    new \App\Validator\Report(null, null, null, $options['existReport']),
                ]
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options, $builder, $readOnly): void {
                $report = $event->getData();
                $form = $event->getForm();

                if (!$report || null === $report->getId()) {
                    $form->add('detail', null, [
                        'label' => 'Detalle: ',
                        'row_attr' => ['style' => 'display:none'],
                    ]+$readOnly);
                }else{
                    $form->add('detail', null, [
                        'label' => 'Detalle: ',
                    ]+$readOnly)
                    ->add('observation', null, [
                        'label' => 'Observaciones: '
                    ])
                    ->add('technical', EntityType::class, [
                        'label' => 'Técnico: ',
                        'class' => User::class,
                        'query_builder' => function (EntityRepository $er) use ($options) {
                            return $er->findTechnical();
                        }
                    ]+$readOnly)
                    ->add('solution', HiddenType::class);
                }
            })
            ->add('priority', PriorityEnumType::class, [
                'label' => 'Prioridad: '
            ]+$readOnly)
            ->add('flaw', FlawEnumType::class, [
                'label' => 'Desperfecto: ',
                'attr' => ['data-action' => 'report#selectFlaw']
            ]+$readOnly)
            ->add('aim', AimEnumType::class, [
                'label' => 'Función: '
            ]+$readOnly)

            ->add('boss', EntityType::class, [
                'label' => 'Responsable: ',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->findBosses();
                }
            ]+$readOnly)
        ;

        $builder->get('equipment')->addModelTransformer($this->equipmentToIdTransformer);
        $builder->get('flaw')->addModelTransformer($this->flawEnumToStringTransformer);
        $builder->get('interruptionReason')->addModelTransformer($this->interruptionReasonEnumToStringTransformer);
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
