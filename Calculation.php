<?php

namespace bdc\Calculation;

use Carbon\Carbon;

class Calculation
{

    public static function weekDays(Carbon $start, Carbon $end): int
    {
    
        $reverse = $end->isBefore($start);
        if($reverse) {
          [$start, $end] = [$end, $start];
        }

        $startDay = $start->day;
        $totalDays = abs($end->diffInDays($start));
        $containedSundays = self::containedPeriodicValues($startDay, $totalDays + $startDay, 0, 7);
        $containedSaturdays = self::containedPeriodicValues($startDay, $totalDays + $startDay, 6, 7);
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
    
    public static function nearestPeriodicValue($point, $value, $period)
    {
        $relation = ($value - $point) / $period;

        $equidistant = !(fmod($relation, 0.5)) && $relation % 1;

        $mod = $equidistant ? $period : 0;

        return $mod + ($value - $period * round($relation));
    }

    public static function containedPeriodicValues(Carbon $start, Carbon $end, $value, $period): int
    {
        if ($start === $end) {
            return 0;
        }

        if ($start > $end) {
            $newEnd = $start;
            $start = $end;
            $end = $newEnd;
        }

        $end--;

        $nearest = self::nearestPeriodicValue($start, $value, $period);

        if ($nearest - $start < 0) {
            $nearest += $period;
        }

        if (($nearest - $start) > ($end - $start)) {
            return 0;
        } else {
            return 1 + (int)(($end - $nearest) / $period);
        }
    }
    
}
