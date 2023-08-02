<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AcademicYearConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AcademicYearConstraint) {
            throw new UnexpectedTypeException($constraint, AcademicYearConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!preg_match('/^\d{4}-\d{4}$/', $value)) {
            $this->context->buildViolation($constraint->invalidFormat)->addViolation();

            return;
        }

        $years = explode('-', $value);

        if (2 !== count($years)) {
            $this->context->buildViolation($constraint->invalidFormat)->addViolation();

            return;
        }

        foreach ($years as $year) {
            if (!is_numeric($year)) {
                $this->context->buildViolation($constraint->invalidFormat)->addViolation();

                return;
            }
        }

        if ((int) $years[0] + 1 !== (int) $years[1]) {
            $this->context->buildViolation($constraint->nonConsecutiveYears)->addViolation();
        }
    }
}
