<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AcademicYearConstraint extends Constraint
{
    public string $invalidFormat = 'validation.academic_year_constraint.format';

    public string $nonConsecutiveYears = 'validation.academic_year_constraint.consecutive_years';
}
