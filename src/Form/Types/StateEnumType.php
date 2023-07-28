<?php

namespace App\Form\Types;

use App\Entity\Enums\State;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StateEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', State::class)
            ->setDefault('choices', static fn (Options $options): array => $options['class']::cases())
            ->setDefault('choice_label', State::getLabel())
            ->setDefault('choice_value', State::getValue())
            //->setDefault('placeholder', '-Seleccione-')
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
