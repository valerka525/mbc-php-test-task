<?php

namespace App\Services;

class HolidayChecker
{
    static $dates = array(
        'New Year' => array('Weekday' => null, 'Week' => null, 'Month' => 1, 'Day' => 1),
        'Christmas' => array('Weekday' => null, 'Week' => null, 'Month' => 1, 'Day' => 7),
        'Labor Day' => array('Weekday' => null, 'Week' => null, 'Month' => 5, 'Day' => array(1, 2, 3, 4, 5, 6, 7)),
        'Jr. Day' => array('Weekday' => 1, 'Week' => 3, 'Month' => 1, 'Day' => null),
        'Some Day' => array('Weekday' => 1, 'Week' => 5, 'Month' => 3, 'Day' => null),
        'Thanksgiving Day' => array('Weekday' => 4, 'Week' => 4, 'Month' => 11, 'Day' => null),
    );

    public static function check($valid)
    {
        function getWeek($timestamp) {
            $week_year = date('W',$timestamp);
            $year = date('Y',$timestamp);
            $month = date('m',$timestamp);
            $prev_month = date('m',$timestamp) -1;
            if($month != 1 ){
                $last_day_prev = $year."-".$prev_month."-1";
                $last_day_prev = date('t',strtotime($last_day_prev));
                $week_year_last_mon = date('W',strtotime($year."-".$prev_month."-".$last_day_prev));
                $week_year_first_this = date('W',strtotime($year."-".$month."-1"));
                if($week_year_first_this == $week_year_last_mon){
                    $week_diff = 0;
                }
                else {
                    $week_diff = 1;
                }
                if($week_year ==1 && $month == 12 ){
                    $week_year = 53;
                }
                $week = $week_year-$week_year_last_mon + 1 +$week_diff;
            }
            else {
                $week_year_first_this = date('W',strtotime($year."-01-1"));
                if($week_year_first_this ==52 || $week_year_first_this ==53){
                    if($week_year == 52 || $week_year == 53){
                        $week =1;
                    }
                    else {
                        $week = $week_year + 1;
                    }
                }
                else {
                    $week = $week_year;
                }
            }
            return $week;
        }

        $input_str = implode($valid);
        $input_timestamp = strtotime($input_str);
        $input_day = date('j', $input_timestamp);
        $input_month = date('n', $input_timestamp);
        $input_weekday = date('w', $input_timestamp);
        $input_week = getWeek($input_timestamp);
        $input_year = date('Y', $input_timestamp);

        foreach (self::$dates as $key => $value) {

            if((!is_array($value['Day'])) &&
                (($value['Day'] == $input_day) OR (($input_weekday == 1) &&
                        ((date('w', (strtotime($value['Day'] . '.' .
                                $value['Month'] . '.' . $input_year)))) == 0 OR 6) &&
                        ($input_day - $value['Day'] <= 2))) &&
                ($value['Month'] == $input_month))
            {
                return ("It's " . $key . " on that date!");
            } elseif ((is_array($value['Day'])) &&
                (array_search($input_day, $value['Day']) !== false) &&
                ($value['Month'] == $input_month))
            {
                return ("It's " . $key . " on that date!");
            } elseif (((($value['Weekday'] == $input_weekday) && ($value['Week'] == $input_week)) OR
                    (($input_weekday == 1) && ($value['Weekday'] == 0 OR 6) &&
                        ($input_week - $value['Week'] == 1))) &&
                ($value['Month'] == $input_month))
            {
                return ("It's " . $key . " on that date!");
            }
        }
    }
}
