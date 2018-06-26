<?php
/**
* requires PHP 5.4+
*/

include __DIR__ . '/lib/CustomCalendar.functions.php';
include __DIR__ . '/lib/CustomCalendarDate.class.php';

$inputDate = '17.11.2013';

// Single function version
echo $inputDate . ' is ' . dayName($inputDate) . PHP_EOL;

// Class version
$date = new CustomCalendarDate($inputDate);
echo $inputDate . ' is ' . $date->getDayName() . PHP_EOL;

// CSV extract
$date->createCSV(
    '10000_days.csv',
    1985,
    1,
    1
);

echo 'Done' . PHP_EOL;