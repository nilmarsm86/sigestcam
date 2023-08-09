<?php

namespace App\Form\Types;

use App\Entity\Municipality;
use App\Entity\Province;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $provinceConstraints = [];
        if(!$options['province']){
            $provinceConstraints = [
                new NotBlank(message: 'Seleccione una provincia.')
            ];
        }

        $municipalityConstraints = [];
        if(!$options['municipality']){
            $municipalityConstraints = [
                new NotBlank(message: 'Seleccione un municipio.')
            ];
        }

        $builder
            ->add('province', EntityType::class, [
                'class' => Province::class,
                'attr' => [
                    'data-model' => 'province'
                ],
                'placeholder' => '-Seleccione-',
                'label' => 'Provincia:',
                'label_attr' => [
                    'class' => 'fw-bold'
                ],
                'mapped' => false,
                'constraints' => $provinceConstraints
            ])
            ->add('municipality', EntityType::class, [
                'class' => Municipality::class,
                'placeholder' => $options['province'] ? '-Seleccione provincia-' : '-Seleccione-',
                'query_builder' => function (EntityRepository $er) use ($options): QueryBuilder|array {
                    return $er->createQueryBuilder('m')->where('m.province = '.$options['province']);
                },
                'disabled' => $options['province'] ? false : true,
                'attr' => [
                    'data-model' => 'municipality'
                ],
                'label' => 'Municipio:',
                'label_attr' => [
                    'class' => 'fw-bold'
                ],
                'constraints' => $municipalityConstraints
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'province' => 0,
            'municipality' => 0,
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
    }

}
