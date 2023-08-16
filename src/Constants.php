<?php

namespace App;

final class Constants
{
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public static function getAvailableWeekdays()
    {
        return [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
        ];
    }
}
