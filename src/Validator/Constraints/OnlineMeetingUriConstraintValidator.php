<?php

namespace App\Validator\Constraints;

use App\Entity\TutoringSession;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class OnlineMeetingUriConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof OnlineMeetingUriConstraint) {
            throw new UnexpectedTypeException($constraint, OnlineMeetingUriConstraint::class);
        }

        $object = $this->context->getObject();

        if (!$object instanceof TutoringSession) {
            throw new UnexpectedValueException($value, TutoringSession::class);
        }

        if (!$object->getIsRemote()) {
            return;
        }

        if (null === $value || '' === $value) {
            $this->context->buildViolation($constraint->empty)->addViolation();
        }
    }
}
