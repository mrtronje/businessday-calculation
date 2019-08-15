<?php

namespace Bdc;

use Carbon\Carbon;

class NumericHelper
{
    private static function nearestPeriodicValue($point, $value, $period)
    {
        $relation = ($value - $point) / $period;
        $equidistant = !(fmod($relation, 0.5)) && $relation % 1;
        $mod = $equidistant ? $period : 0;
        return $mod + ($value - $period * round($relation));
    }

    public static function containedPeriodicValues(int $start, int $end, $value, $period): int
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

    public static function binarySearch($needle, array $haystack): int
    {
        if (count($needle) === 0) {
            return -1;
        }

        $key = -1;
        $low = 0;
        $high = count($needle) - 1;

        while ($high >= $low) {
            $mid = (int)floor(($high + $low) / 2);
            $cmp = $needle <=> $haystack[$mid];

            if ($cmp < 0) {
                $high = $mid - 1;
            } elseif ($cmp > 0) {
                $low = $mid + 1;
            } else {
                $key = $mid;
                break;
            }
        }
        return $key;
    }
}
