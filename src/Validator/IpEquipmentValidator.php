<?php

namespace App\Validator;

use App\Repository\EquipmentRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validate IP for all equipment
 */
class IpEquipmentValidator extends ConstraintValidator
{
    /**
     * @param EquipmentRepository $equipmentRepository
     */
    public function __construct(private readonly EquipmentRepository $equipmentRepository)
    {
    }

    /**
     * @param $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IpEquipment) {
            throw new UnexpectedTypeException($constraint, IpEquipment::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if(!is_null($constraint->equipment)){
            $equipment = $constraint->equipment;
            if($equipment->getId() !== null){
                if($equipment->getIp() !== $value){
                    $this->find($value);
                }
            }else{
                $this->find($value);
            }
        }else{
            $this->find($value);
        }
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function find(mixed $value): void
    {
        $existing = $this->equipmentRepository->findBy(['ip' => $value]);
        if ($existing && count($existing) >= 1) {
            $this->context->buildViolation('Ya existe un equipo con este IP (' . $value . ')')->addViolation();
        }
    }
}
