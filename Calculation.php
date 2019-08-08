<?php

namespace Bdc;

use Carbon\Carbon;
use Bdc\NumericHelper;

class Calculation
{

    //replace Carbon with date for more acceptance
    public static function weekDays(Carbon $start, Carbon $end): int
    {
       return self::workdays($start, $end, [0, 1, 1, 1, 1, 1, 0]);
    }
    
    /**
    * $workweek is a array where the keys are the days of the week. $i=0 -> Sunday, $i=1 -> Monday...
    * TODO: better comments
    */
    public static function workDays(Carbon $start, Carbon $end, array $workweek = [0, 1, 1, 1, 1, 1, 0]): int
    {
        if (count($workweek) !== 7) {
            throw new Exception('$workweek needs 7 values');
        }

        $reverse = $end->isBefore($start);
        if ($reverse) {
            [$start, $end] = [$end, $start];
        }

        $coefficient = $reverse ? -1 : 1;


        $startDay = $start->day;
        $totalDays = abs($end->diffInDays($start));
        $containedFreeDays = 0;

        for ($i = 0; $i < count($workweek); $i++) {
            if ($workweek[$i] !== 1) {
                $containedDays = NumericHelper::containedPeriodicValues($startDay, $totalDays + $startDay, $i, 7);
                $containedFreeDays += $containedDays * (1 - $workweek[$i]);
            }
        }

        return $coefficient * ($totalDays - $containedFreeDays);
    }
    
    public static function addWeekDays(Carbon $date, float $amount): Carbon
    {
        if ($amount === 0 || is_nan($amount)) {
            return $date;
        }

        $sign = self::determineSign($amount);
        $day = $date->day;
        $absIncrement = abs($amount);

        $days = 0;

        if (($day === 0 && $sign === -1) || ($day === 6 && $sign === 1)) {
            $days = 1;
        }

        $paddedAbsIncrement = $absIncrement;
        if ($day !== 0 && $day !== 6 && $sign > 0) {
            $paddedAbsIncrement += $day;
        } else if ($day !== 0 && $day !== 6 && $sign < 0) {
            $paddedAbsIncrement += 6 - $day;
        }
        $weekendsInbetween = max(
                floor($paddedAbsIncrement / 5) - 1,
                0
            ) + ($paddedAbsIncrement > 5 && $paddedAbsIncrement % 5 > 0 ? 1 : 0);

        $days += $absIncrement + $weekendsInbetween * 2;

        $date->addDays($sign * $days);
        return $date;
    }

    public static function subtractWeekDays(Carbon $date, float $amount): Carbon: 
    {
        return self::addWeekDays($date, -$amount);
    }

    public static function determineSign(float $number): int
    {
        return $number <=> 0;
    }   
}
