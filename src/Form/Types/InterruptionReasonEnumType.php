<?php

namespace App\Form\Types;

use App\Entity\Enums\InterruptionReason;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterruptionReasonEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', InterruptionReason::class)
            ->setDefault('choices', static fn (Options $options): array => $options['class']::cases())
            ->setDefault('choice_label', InterruptionReason::getLabel())
            ->setDefault('choice_value', InterruptionReason::getValue())
            ->setDefault('placeholder', '-Seleccione-')
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
