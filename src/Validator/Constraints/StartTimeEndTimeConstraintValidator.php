<?php

namespace App\Validator\Constraints;

use App\Entity\Tutoring;
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

        if ($value instanceof BatchTutoringSessionCreationModel) {
            $startTime = $value->getStartTime();
            $endTime = $value->getEndTime();
        } elseif ($value instanceof Tutoring) {
            $startTime = $value->getDefaultStartTime();
            $endTime = $value->getDefaultEndTime();
        } else {
            throw new UnexpectedValueException($value, sprintf('%s or %s', BatchTutoringSessionCreationModel::class, Tutoring::class));
        }

        if (!$startTime instanceof DateTimeInterface || !$endTime instanceof DateTimeInterface) {
            return;
        }

        if ($startTime > $endTime) {
            $this->context->buildViolation($constraint->startTimeAfterEndTime)->addViolation();
        }
    }
}
