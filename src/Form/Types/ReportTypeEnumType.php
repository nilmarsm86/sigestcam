<?php

namespace App\Form\Types;

use App\Entity\Enums\ReportType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportTypeEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', ReportType::class)
            ->setDefault('choices', static fn (Options $options): array => $options['class']::cases())
            ->setDefault('choice_label', ReportType::getLabel())
            ->setDefault('choice_value', ReportType::getValue())
            //->setDefault('placeholder', '-Seleccione-')
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
