<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, Password::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!preg_match('/^.{6,}$/', $value)) {
            $this->context->buildViolation('La contraseña debe tener como mínimo {{ limit }} caracteres')
                ->setParameter('{{ limit }}', 6)
                ->addViolation();
        }

        if (!preg_match('/^(?=.*[A-Z]).{6,}$/', $value)) {
            $this->context->buildViolation('La contraseña debe tener como mínimo {{ limit }} caracter en mayúscula')
                ->setParameter('{{ limit }}', 1)
                ->addViolation();
        }

        if (!preg_match('/^(?=.*[0-9])(?=.*[A-Z]).{6,20}$/', $value)) {
            $this->context->buildViolation('La contraseña debe tener como mínimo {{ limit }} número.')
                ->setParameter('{{ limit }}', 1)
                ->addViolation();
        }

        if (!preg_match('/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{6,20}$/', $value)) {
            $this->context->buildViolation('La contraseña debe tener como mínimo {{ limit }} caracter especial.')
                ->setParameter('{{ limit }}', 1)
                ->addViolation();
        }
    }
}
