<?php

namespace App\Form\DataTransformer;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EquipmentToIdTransformer implements DataTransformerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param $equipment
     * @return string
     */
    public function transform($equipment): string
    {
        if($equipment === 0){
            return 0;
        }

        if (null === $equipment) {
            return '';
        }

        if(is_int($equipment)){
            return $equipment;
        }

        return $equipment->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param $id
     * @return Equipment|null
     */
    public function reverseTransform($id): ?Equipment
    {
        // no issue number? It's optional, so that's ok
        if (!$id) {
            return null;
        }

        $equipment = $this->entityManager->getRepository(Equipment::class)->find($id);

        if (null === $equipment) {
            $privateErrorMessage = "No existe un equipo con este ID: $id.";
            $publicErrorMessage = 'El ID "{{ value }}" no es de un equipo válido.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $id,
            ]);

            throw $failure;
        }

        return $equipment;
    }
}