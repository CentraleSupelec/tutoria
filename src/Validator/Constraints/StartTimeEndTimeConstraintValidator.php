<?php

namespace App\Validator\Constraints;

use App\Model\BatchTutoringSessionCreationModel;
use DateTimeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class StartTimeEndTimeConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof StartTimeEndTimeConstraint) {
            throw new UnexpectedTypeException($constraint, StartTimeEndTimeConstraint::class);
        }

        if (!$value instanceof BatchTutoringSessionCreationModel) {
            throw new UnexpectedValueException($value, BatchTutoringSessionCreationModel::class);
        }

        if (!$value->getStartTime() instanceof DateTimeInterface || !$value->getEndTime() instanceof DateTimeInterface) {
            return;
        }

        if ($value->getStartTime() > $value->getEndTime()) {
            $this->context->buildViolation($constraint->startTimeAfterEndTime)->addViolation();
        }
    }
}
