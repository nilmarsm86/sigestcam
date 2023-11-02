<?php

namespace App\Form\Types;

use App\Entity\Enums\Priority;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriorityEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', Priority::class)
            ->setDefault('choices', static fn (Options $options): array => $options['class']::cases())
            ->setDefault('choice_label', Priority::getLabel())
            ->setDefault('choice_value', Priority::getValue())
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
