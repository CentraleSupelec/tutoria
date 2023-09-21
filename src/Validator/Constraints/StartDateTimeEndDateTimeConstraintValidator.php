<?php

namespace App\Validator\Constraints;

use App\Entity\TutoringSession;
use DateTimeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class StartDateTimeEndDateTimeConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof StartDateTimeEndDateTimeConstraint) {
            throw new UnexpectedTypeException($constraint, StartDateTimeEndDateTimeConstraint::class);
        }

        if (!$value instanceof TutoringSession) {
            throw new UnexpectedValueException($value, TutoringSession::class);
        }

        if (!$value->getStartDateTime() instanceof DateTimeInterface || !$value->getEndDateTime() instanceof DateTimeInterface) {
            return;
        }

        if ($value->getStartDateTime()->format('d/m/Y') !== $value->getEndDateTime()->format('d/m/Y')) {
            $this->context->buildViolation($constraint->notSameDay)
                ->atPath('endDateTime')
                ->addViolation();

            return;
        }

        if ($value->getStartDateTime() > $value->getEndDateTime()) {
            $this->context->buildViolation($constraint->startDateTimeAfterEndDateTime)
                ->atPath('endDateTime')
                ->addViolation();
        }
    }
}
