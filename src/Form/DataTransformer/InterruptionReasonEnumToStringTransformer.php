<?php

namespace App\Form\DataTransformer;

use App\Entity\Enums\InterruptionReason;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class InterruptionReasonEnumToStringTransformer implements DataTransformerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param $interruptionReason
     * @return string
     */
    public function transform($interruptionReason): InterruptionReason|string|null
    {
        if($interruptionReason === 0){
            return 0;
        }

        if (null === $interruptionReason) {
            return null;
        }

        if(is_string($interruptionReason)){
            return InterruptionReason::tryFrom($interruptionReason);
        }

        return $interruptionReason->value;
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param $value
     * @return InterruptionReason|null
     */
    public function reverseTransform($value): ?InterruptionReason
    {
        // no issue number? It's optional, so that's ok
        if (!$value) {
            return null;
        }

        if(is_string($value)){
            return InterruptionReason::tryFrom($value);
        }

        return $value;
    }
}