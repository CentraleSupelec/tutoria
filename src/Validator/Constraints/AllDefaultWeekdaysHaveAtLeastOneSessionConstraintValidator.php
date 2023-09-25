<?php

namespace App\Validator\Constraints;

use App\Entity\Tutoring;
use App\Model\BatchTutoringSessionCreationModel;
use App\Utils\DateUtils;
use DateTimeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AllDefaultWeekdaysHaveAtLeastOneSessionConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AllDefaultWeekdaysHaveAtLeastOneSessionConstraint) {
            throw new UnexpectedTypeException($constraint, AllDefaultWeekdaysHaveAtLeastOneSessionConstraint::class);
        }

        if ($value instanceof BatchTutoringSessionCreationModel) {
            $startDate = $value->getStartDate();
            $endDate = $value->getEndDate();
            $selectedWeekdays = $value->getWeekDays();
        } else {
            throw new UnexpectedValueException($value, sprintf('%s or %s', BatchTutoringSessionCreationModel::class, Tutoring::class));
        }

        if (!$startDate instanceof DateTimeInterface || !$endDate instanceof DateTimeInterface || [] === $selectedWeekdays) {
            return;
        }

        // Retrieve all the dates that fall between the chosen start and end dates for the selected weekdays.
        $allSelectedDates = DateUtils::getAllDatesBetweenDatesByWeekdays($startDate, $endDate, $selectedWeekdays);

        // Make sure that we have selected enough dates to cover each selected weekday at least once.
        if (count($allSelectedDates) < count($selectedWeekdays)) {
            $this->context->buildViolation($constraint->notAllDefaultWeekdaysAppearAtLeastOnce)
                ->atPath('startDate')
                ->addViolation();
        }
    }
}
