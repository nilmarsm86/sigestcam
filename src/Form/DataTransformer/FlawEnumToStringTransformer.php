<?php

namespace App\Form\DataTransformer;

use App\Entity\Enums\Flaw;
use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class FlawEnumToStringTransformer implements DataTransformerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param $flaw
     * @return Flaw|string|null
     */
    public function transform($flaw): Flaw|string|null
    {
        if($flaw === 0){
            return 0;
        }

        if (null === $flaw) {
            return null;
        }

        if(is_string($flaw)){
            return Flaw::tryFrom($flaw);
        }

        return $flaw->value;
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param $value
     * @return Equipment|null
     */
    public function reverseTransform($value): ?Flaw
    {
        // no issue number? It's optional, so that's ok
        if (!$value) {
            return null;
        }

        if(is_string($value)){
            return Flaw::tryFrom($value);
        }

        return $value;
    }
}