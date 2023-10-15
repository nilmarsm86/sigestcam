<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UsernameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Username) {
            throw new UnexpectedTypeException($constraint, Username::class);
        }

        /*
         * @var Username $constraint
         */
        if (null === $value || '' === $value) {
            return;
        }

        // strange characters
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $value)) {
            $this->context->buildViolation('El nombre de usuario debe contener solo caracteres válidos.')
                ->addViolation();
        }

        // uppercase characters
        if (preg_match('/[A-Z]/', $value)) {
            $this->context->buildViolation('El nombre de usuario debe contener solo minúsculas.')
                ->addViolation();
        }
    }
}
