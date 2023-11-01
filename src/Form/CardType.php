<?php

namespace App\Form;

use App\Entity\Card;
use App\Entity\Msam;
use App\Repository\MsamRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardType extends AbstractType
{
    public function __construct(private readonly MsamRepository $msamRepository)
    {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        if($options['msam'] instanceof Msam){
//            $msam = $options['msam'];
//        }else{
//            $msam = $this->msamRepository->find($options['msam']);
//        }
        $msam = ($options['msam'] instanceof Msam) ? $options['msam'] : $this->msamRepository->find($options['msam']);
        $position = $msam->connectedCards() + 1;

        $builder->add('name', null, [
            'label' => 'Nombre:'
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($position): void {
            $msam = $event->getData();
            $form = $event->getForm();

            if (!$msam || null === $msam->getId()) {
                $form->add('slot', HiddenType::class, [
                    'data' => $position
                ]);

                $form->add('portsAmount', null, [
                    'attr' => [
                        'min' => 1,
                        'max' => 16,
                        'list' => 'ports_amount'
                    ],
                    'data' => 1
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
            'attr' => [
                'novalidate' => true,
                'class' => 'msam_card'
            ],
            'msam' => null,
        ]);

        $resolver->setAllowedTypes('msam', 'int');
    }
}
