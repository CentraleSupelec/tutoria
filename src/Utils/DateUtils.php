<?php

namespace App\Utils;

use App\Constants;
use DateTimeInterface;

class DateUtils
{
    public static function getAllDatesBetweenDatesByWeekdays(DateTimeInterface $startDate, DateTimeInterface $endDate, array $selectedWeekdays): array
    {
        $result = [];
        $selectedWeekdaysIndexes = [];
        $weekDays = Constants::getAvailableWeekdays();

        foreach ($weekDays as $index => $dayName) {
            if (in_array($dayName, $selectedWeekdays)) {
                $selectedWeekdaysIndexes[] = $index + 1;
            }
        }

        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->format('N');

            if (in_array($dayOfWeek, $selectedWeekdaysIndexes)) {
                $result[] = clone $currentDate;
            }
            if ($dayOfWeek > 4) {
                // Skip Saturday and Sunday
                $currentDate->modify(sprintf('+%s day', 8 - $dayOfWeek));
            } else {
                $currentDate->modify('+1 day');
            }
        }

        return $result;
    }
}
