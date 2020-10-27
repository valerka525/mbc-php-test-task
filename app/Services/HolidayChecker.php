<?php

namespace App\Services;

class HolidayChecker
{
    private static $holidays = [
        'New Year' => ['Weekday' => null, 'Week' => null, 'Month' => 1, 'Day' => 1, 'Period' => null],
        'Christmas' => ['Weekday' => null, 'Week' => null, 'Month' => 1, 'Day' => 7, 'Period' => null],
        'Labor Day' => ['Weekday' => null, 'Week' => null, 'Month' => 5, 'Day' => 1, 'Period' => 6],
        'Jr. Day' => ['Weekday' => 0, 'Week' => 2, 'Month' => 1, 'Day' => null, 'Period' => null],
        'Unknown Day' => ['Weekday' => 0, 'Week' => -1, 'Month' => 4, 'Day' => null, 'Period' => null],
        'Thanksgiving Day' => ['Weekday' => 3, 'Week' => 3, 'Month' => 11, 'Day' => null, 'Period' => null],
    ];

    private static function findFirstMonday($year, $month){
        for ($day = 1; $day <= 31; $day++)
        {
            $time = mktime(0, 0, 0, $month, $day, $year);
            if (date('N', $time) == 1)
            {
                return $time;
            }
        }
    }

    public static function check($valid)
    {
        $inputStr = implode($valid);
        $inputTimestamp = strtotime($inputStr);
        $inputYear = date('Y', $inputTimestamp);
        $oneDayTimestamp = 86400;
        $oneWeekTimestamp = 604800;
        $holidaysTimeStamps = [];

        foreach (self::$holidays as $key => $value){
            switch(true){
                case isset($value['Period']) : $firstDay = $currentDay = mktime(0, 0, 0, $value['Month'], $value['Day'], $inputYear);
                                               $holidayNumber = 1;
                                               do {$holidaysTimeStamps["$key $holidayNumber"] = $currentDay; $currentDay+=$oneDayTimestamp; $holidayNumber++;}
                                               while ($currentDay <= $firstDay + $value['Period']*$oneDayTimestamp); break;
                case isset($value['Day']) : $holidaysTimeStamps[$key] = mktime(0, 0, 0, $value['Month'], $value['Day'], $inputYear); break;
                case isset($value['Weekday']) : $holidaysTimeStamps[$key] = self::findFirstMonday($inputYear, $value['Month']) +
                                                $value['Week']*$oneWeekTimestamp + $value['Weekday']*$oneDayTimestamp; break;
            }
        }

        $holidayResult = (array_search($inputTimestamp, $holidaysTimeStamps));
        if ($holidayResult){
            return "It's $holidayResult on that date!";
        } else {
            foreach ($holidaysTimeStamps as $key => $value){
                switch (true){
                    case (date('w', $value) == 6) && ($inputTimestamp - $value == $oneDayTimestamp*2) &&
                        (date('w', $inputTimestamp) == 1) :
                        return "It's day off because of $key falls on Saturday";
                    case (date('w', $value) == 0) && ($inputTimestamp - $value == $oneDayTimestamp) &&
                        (date('w', $inputTimestamp) == 1) :
                        return "It's day off because of $key falls on Sunday";
                }
            }
        }
    }
}
