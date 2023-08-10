<?php

namespace App\Validator\Constraints;

use App\Model\BatchTutoringSessionCreationModel;
use DateTimeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class StartDateEndDateConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof StartDateEndDateConstraint) {
            throw new UnexpectedTypeException($constraint, StartDateEndDateConstraint::class);
        }

        if (!$value instanceof BatchTutoringSessionCreationModel) {
            throw new UnexpectedValueException($value, BatchTutoringSessionCreationModel::class);
        }

        if (!$value->getStartDate() instanceof DateTimeInterface || !$value->getEndDate() instanceof DateTimeInterface) {
            return;
        }

        if ($value->getStartDate() > $value->getEndDate()) {
            $this->context->buildViolation($constraint->startDateAfterEndDate)->addViolation();
        }
    }
}
