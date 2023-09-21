<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class OnlineMeetingUriConstraint extends Constraint
{
    public string $empty = 'validation.online_meeting_uri_constraint.empty';
}
