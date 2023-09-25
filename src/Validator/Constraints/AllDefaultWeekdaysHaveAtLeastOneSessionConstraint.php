<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AllDefaultWeekdaysHaveAtLeastOneSessionConstraint extends Constraint
{
    public string $notAllDefaultWeekdaysAppearAtLeastOnce = 'validation.batch_tutoring_session_creation_model.dates_outside_default_weekdays';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
