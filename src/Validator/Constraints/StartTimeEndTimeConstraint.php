<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class StartTimeEndTimeConstraint extends Constraint
{
    public string $startTimeAfterEndTime = 'validation.start_time_end_time_constraint.start_time_after_end_time';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
