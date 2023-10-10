<?php

namespace App\Form\Types;

use App\Entity\Enums\ReportState;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportStateEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', ReportState::class)
            ->setDefault('choices', static fn (Options $options): array => $options['class']::cases())
            ->setDefault('choice_label', ReportState::getLabel())
            ->setDefault('choice_value', ReportState::getValue())
            //->setDefault('placeholder', '-Seleccione-')
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
