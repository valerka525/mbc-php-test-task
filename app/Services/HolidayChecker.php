<?php

namespace App\Services;

class HolidayChecker
{
    private static $dates = [
        'New Year' => ['Weekday' => null, 'Week' => null, 'Month' => 1, 'Day' => 1],
        'Christmas' => ['Weekday' => null, 'Week' => null, 'Month' => 1, 'Day' => 7],
        'Labor Day' => ['Weekday' => null, 'Week' => null, 'Month' => 5, 'Day' => [1, 2, 3, 4, 5, 6, 7]],
        'Jr. Day' => ['Weekday' => 1, 'Week' => 3, 'Month' => 1, 'Day' => null],
        'Some Day' => ['Weekday' => 1, 'Week' => 5, 'Month' => 3, 'Day' => null],
        'Thanksgiving Day' => ['Weekday' => 4, 'Week' => 4, 'Month' => 11, 'Day' => null],
    ];

    private static function getWeek($timestamp) {
        $weekYear = date('W',$timestamp);
        $year = date('Y',$timestamp);
        $month = date('m',$timestamp);
        $prevMonth = date('m',$timestamp) -1;
        if($month != 1 ){
            $lastDayPrev = $year."-".$prevMonth."-1";
            $lastDayPrev = date('t',strtotime($lastDayPrev));
            $weekYearLastMon = date('W',strtotime($year."-".$prevMonth."-".$lastDayPrev));
            $weekYearFirstThis = date('W',strtotime($year."-".$month."-1"));
            if($weekYearFirstThis == $weekYearLastMon){
                $weekDiff = 0;
            }
            else {
                $weekDiff = 1;
            }
            if($weekYear ==1 && $month == 12 ){
                $weekYear = 53;
            }
            $week = $weekYear-$weekYearLastMon + 1 +$weekDiff;
        }
        else {
            $weekYearFirstThis = date('W',strtotime($year."-01-1"));
            if($weekYearFirstThis ==52 || $weekYearFirstThis ==53){
                if($weekYear == 52 || $weekYear == 53){
                    $week =1;
                }
                else {
                    $week = $weekYear + 1;
                }
            }
            else {
                $week = $weekYear;
            }
        }
        return $week;
    }

    public static function check($valid)
    {
        $inputStr = implode($valid);
        $inputTimestamp = strtotime($inputStr);
        $inputDay = date('j', $inputTimestamp);
        $inputMonth = date('n', $inputTimestamp);
        $inputWeekday = date('w', $inputTimestamp);
        $inputWeek = self::getWeek($inputTimestamp);
        $inputYear = date('Y', $inputTimestamp);

        foreach (self::$dates as $key => $value) {

            if((!is_array($value['Day'])) &&
                (($value['Day'] == $inputDay) || (($inputWeekday == 1) &&
                        ((date('w', (strtotime($value['Day'] . '.' .
                                $value['Month'] . '.' . $inputYear)))) == 0 || 6) &&
                        ($inputDay - $value['Day'] <= 2))) &&
                ($value['Month'] == $inputMonth))
            {
                return "It's $key on that date!";
            } elseif ((is_array($value['Day'])) &&
                (array_search($inputDay, $value['Day']) !== false) &&
                ($value['Month'] == $inputMonth))
            {
                return "It's $key on that date!";
            } elseif (((($value['Weekday'] == $inputWeekday) && ($value['Week'] == $inputWeek)) ||
                    (($inputWeekday == 1) && ($value['Weekday'] == 0 || 6) &&
                        ($inputWeek - $value['Week'] == 1))) &&
                ($value['Month'] == $inputMonth))
            {
                return "It's $key on that date!";
            }
        }
    }
}
