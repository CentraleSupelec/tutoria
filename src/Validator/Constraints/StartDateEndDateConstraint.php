<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class StartDateEndDateConstraint extends Constraint
{
    public string $startDateAfterEndDate = 'validation.start_date_end_date_constraint.start_date_after_end_date';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
