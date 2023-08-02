<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AcademicYearConstraint extends Constraint
{
    public string $invalidFormat = 'validation.activity.academic_year_format';

    public string $nonConsecutiveYears = 'validation.activity.academic_year_consecutive_years';
}
