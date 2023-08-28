<?php

namespace App\Form\Types;

use App\Entity\Municipality;
use App\Entity\Province;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function __construct(
        private readonly ProvinceRepository $provinceRepository,
        private readonly MunicipalityRepository $municipalityRepository,
//        private readonly EntityManagerInterface $entityManager
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $province = $this->provinceRepository->find($options['province']);
        $municipality = $this->municipalityRepository->find($options['municipality']);

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
                    'data-model' => 'province',
                    'data-address-target' => "province",
                    'data-action' => 'change->address#selectProvince'
                ],
                'placeholder' => '-Seleccione-',
                'label' => 'Provincia:',
                'label_attr' => [
                    'class' => 'fw-bold'
                ],
                'mapped' => false,
                'constraints' => $provinceConstraints,
                'data' => $province
            ])
            ->add('municipality', EntityType::class, [
                'class' => Municipality::class,
                'placeholder' => $options['province'] ? '-Seleccione provincia-' : '-Seleccione-',
                'query_builder' => function (EntityRepository $er) use ($options): QueryBuilder|array {
                    return $er->createQueryBuilder('m')->where('m.province = '.$options['province']);
                },
                'disabled' => $options['province'] ? false : true,
                'attr' => [
                    'data-model' => 'municipality',
                    'data-address-target' => "municipality",
                ],
                'label' => 'Municipio:',
                'label_attr' => [
                    'class' => 'fw-bold'
                ],
                'constraints' => $municipalityConstraints,
                'data' => $municipality
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

//    public function getRealEntity($proxy)
//    {
//        if ($proxy instanceof \Doctrine\Persistence\Proxy) {
//            $proxy_class_name = get_class($proxy);
//            $class_name = $this->entityManager->getClassMetadata($proxy_class_name)->rootEntityName;
//            $this->entityManager->detach($proxy);
//            return $this->entityManager->find($class_name, $proxy->getId());
//        }
//
//        return $proxy;
//    }

}
