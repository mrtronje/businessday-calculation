<?php


class NumericHelper {
  
    private static function nearestPeriodicValue($point, $value, $period)
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
