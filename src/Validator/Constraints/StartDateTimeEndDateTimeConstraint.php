<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class StartDateTimeEndDateTimeConstraint extends Constraint
{
    public string $startDateTimeAfterEndDateTime = 'validation.start_date_time_end_date_time_constraint.start_date_time_after_end_date_time';
    public string $notSameDay = 'validation.start_date_time_end_date_time_constraint.not_same_day';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
