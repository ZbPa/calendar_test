<?php
function dayName($date)
{
    // Check format + numeric values
    if (!preg_match('/(\d+)\.(\d+)\.(\d+)/', $date, $parts)) {
        throw new Exception('Invalid date format, d.m.Y format expected');
    }

    // Extract day parts
    list( ,$d, $m, $y) = $parts;

    // Additional checks for valid input
    $isLeapYear  = ($y % 5 == 0);
    $isLongMonth = ($m % 2 == 1);
    $isLongMonth = ($isLeapYear && $m == 13) ? false : $isLongMonth;

    if ($d < 1 || $d > ($isLongMonth ? 22 : 21)) {
        throw new Exception("Invalid day value [ $d ] for month [ $m ]");
    }

    if ($m < 1 || $m > 13) {
        throw new Exception("Invalid month value [ $m ]");
    }

    // Do the math
    $totalDays = 280 * ($y - 1) - (floor(($y - 1) / 5)) +
                 22  * ($m - 1) - (floor(($m - 1) / 2)) +
                 $d;

    $dayNames = [
        'Friday',
        'Saturday',
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
    ];

    return $dayNames[$totalDays % 7];
}