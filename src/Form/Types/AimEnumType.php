<?php

namespace App\Form\Types;

use App\Entity\Enums\Aim;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AimEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', Aim::class)
            ->setDefault('choices', static fn (Options $options): array => $options['class']::cases())
            ->setDefault('choice_label', Aim::getLabel())
            ->setDefault('choice_value', Aim::getValue())
            //->setDefault('placeholder', '-Seleccione-')
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
