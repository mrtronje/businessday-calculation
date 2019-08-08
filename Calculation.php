<?php

namespace Bdc;

use Carbon\Carbon;
use Bdc\NumericHelper;

class Calculation
{

    //replace Carbo with date for more acceptance
    public static function weekDays(Carbon $start, Carbon $end): int
    {
        $reverse = $end->isBefore($start);
        if($reverse) {
          [$start, $end] = [$end, $start];
        }

        $startDay = $start->day;
        $totalDays = abs($end->diffInDays($start));
        $containedSundays = NumericHelper::containedPeriodicValues($startDay, $totalDays + $startDay, 0, 7);
        $containedSaturdays = NumericHelper::containedPeriodicValues($startDay, $totalDays + $startDay, 6, 7);
        $coefficient = $reverse ? -1 : 1;

        return $coefficient * ($totalDays - ($containedSaturdays + $containedSundays));
    }
    
    public static function addWeekDays(Carbon $date, $amount)
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

    public static function subtractWeekDays(Carbon $date, $amount)
    {
        return self::addWeekDays($date, -$amount);
    }

    public static function determineSign($x)
    {
        $x = +$x;
        return $x > 0 ? 1 : -1;
    }   
}
