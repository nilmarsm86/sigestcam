<?php

namespace App\Validator;

use App\Repository\ReportRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ReportValidator extends ConstraintValidator
{
    public function __construct(private readonly ReportRepository $reportRepository)
    {
    }


    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Report) {
            throw new UnexpectedTypeException($constraint, Report::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if(!$constraint->existReport){
            $existReport = $this->reportRepository->findOpenForEquipment($value);
            if ($existReport) {
                $this->context->buildViolation('Ya el equipo seleccionado está reportado.')
                    ->addViolation();
            }
        }

    }
}
