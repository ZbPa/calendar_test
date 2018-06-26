<?php
class CustomCalendarDate
{
    /**
    * Day names
    *
    * @var mixed[]
    */
    private $days = [
        'Friday',
        'Saturday',
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
    ];

    /**
    *
    * @var sring|array|null $date Date in format d.m.Y or array [Y, m, d]
    */
    private $date = '1.1.1';

    /**
    *
    * @var int
    */
    private $d = 1;

    /**
    *
    * @var int
    */
    private $m = 1;

    /**
    *
    * @var int
    */
    private $y = 1;

    /**
    *
    * @var int
    */
    private $totalDays = 1;

    /**
    *
    * @param sring|array $date Date in format d.m.Y or array [Y, m, d]
    */
    public function __construct($date = null)
    {
        if ($date !== null) {
            $this->setDate($date);
        }
    }

    /**
    * Sets date
    *
    * @param sring|array $date Date in format d.m.Y or array [Y, m, d]
    */
    public function setDate($date)
    {
        if (is_array($date) && count($date) == 3) {
            $this->y = $date[0];
            $this->m = $date[1];
            $this->d = $date[2];
        } else {
            if ($date === null) {
                $this->d = 1;
                $this->m = 1;
                $this->y = 1;
            } else {
                list(, $this->d, $this->m, $this->y) = $this->extractDateParts($date);
            }
        }

        $this->validate($this->y, $this->m, $this->d);
        $this->totalDays = $this->calculateTotalDays($this->y, $this->m, $this->d);

        $this->date = $date;
    }

    /**
    * Extracts numeric date parts
    *
    * @return mixed[]
    */
    private function extractDateParts($date)
    {
        if (!preg_match('/(\d+)\.(\d+)\.(\d+)/', $date, $parts)) {
            throw new Exception('Invalid date format, d.m.Y format expected');
        }

        return $parts;
    }

    /**
    * Checks if date is valid, throws Exception if not
    *
    * @param int $y Year
    * @param int $m Month
    * @param int $d Day
    *
    * @return bool
    */
    private function validate($y, $m, $d)
    {
        $isLeapYear  = self::isLeapYear($y);
        $isLongMonth = self::isLongMonth($m, $isLeapYear);

        if ($d < 1 || $d > ($isLongMonth ? 22 : 21)) {
            throw new Exception("Invalid day value [ $d ] for month [ $m ]" . ($isLeapYear ?: ' of leap year'));
        }

        if ($m < 1 || $m > 13) {
            throw new Exception("Invalid month value [ $m ]");
        }

        if ($y < 0) {
            throw new Exception("Invalid year value [ $y ], year has to be greater than 0");
        }

        return true;
    }

    /**
    * Checks if given year is leap one
    *
    * @param int $y
    *
    * @return bool
    */
    public static function isLeapYear($y)
    {
        return ($y % 5 == 0);
    }

    /**
    * Returns length of given month
    *
    * @param int $m
    * @param bool $isLeapYear
    *
    * @return int
    */
    public static function isLongMonth($m, $isLeapYear)
    {
        $isLongMonth = ($m % 2 == 1);
        $isLongMonth = ($isLeapYear && $m == 13) ? false : $isLongMonth;

        return $isLongMonth;
    }

    /**
    * Calculates total days passed since 1.1.1
    *
    * @param int $y Year
    * @param int $m Month
    * @param int $d Day
    *
    * @return int
    */
    private function calculateTotalDays($y, $m, $d)
    {
        return (
            280 * ($y - 1) - (floor(($y - 1) / 5)) +
            22  * ($m - 1) - (floor(($m - 1) / 2)) +
            $d
        );
    }

    /**
    * Returns name of current day
    *
    * @return string
    */
    public function getDayName()
    {
        if ($this->date === null) {
            throw new Exception('No date set');
        }

        return $this->days[$this->totalDays % 7];
    }

    /**
    * Creates CSV file with number of day names from given date
    *
    * @param string $filename
    * @param int $y
    * @param int $m
    * @param int $d
    * @param int|null $days
    */
    public function createCSV($filename, $y, $m, $d, $days = 10000)
    {
        $fh = fopen($filename, 'w');

        for ($i = 0; $i < $days; $i++) {
            $this->setDate([$y, $m, $d]);

            fputcsv(
                $fh,
                [
                    sprintf("%02d.%02d.%04d", $d, $m, $y),
                    $this->getDayName()
                ],
                ';',
                "'"
            );

            $isLeapYear = self::isLeapYear($y);
            $monthLength = (self::isLongMonth($m, $isLeapYear)) ? 22 : 21;

            // Increment date parts
            $d++;

            if ($d > $monthLength) {
                $d = 1;
                $m++;

                if ($m > 13) {
                    $m = 1;
                    $y++;
                }
            }
        }

        fclose($fh);
    }
}