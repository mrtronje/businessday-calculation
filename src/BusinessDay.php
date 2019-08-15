<?php

namespace Bdc;

use Carbon\Carbon;
use Bdc\NumericHelper;
use phpDocumentor\Reflection\Types\Static_;

class BusinessDay
{
    protected static $holidays = [];

    protected static $workweek = [0, 1, 1, 1, 1, 1, 0];

    /**
     * $workweek is a array where the keys are the days of the week. $i=0 -> Sunday, $i=1 -> Monday...
     * TODO: better comments
     */
    public static function workDaysBetween(\DateTime $start, \DateTime $end): int
    {
        if (count(self::$workweek) !== 7) {
            throw new \Exception('$workweek needs 7 values');
        }

        [$start, $end] = [Carbon::instance($start), Carbon::instance($end)];

        $reverse = $end->isBefore($start);

        if ($reverse) {
            [$start, $end] = [$end, $start];
        }

        $coefficient = $reverse ? -1 : 1;


        $startDay = $start->day;
        $totalDays = abs($end->diffInDays($start));
        $containedFreeDays = 0;

        for ($i = 0; $i < count(self::$workweek); $i++) {
            if (self::$workweek[$i] !== 1) {
                $containedDays = NumericHelper::containedPeriodicValues($startDay, $totalDays + $startDay, $i, 7);
                $containedFreeDays += $containedDays * (1 - self::$workweek[$i]);
            }
        }

        return $coefficient * ($totalDays - $containedFreeDays);
    }

    public static function addWorkDays(\DateTime $date, float $amount): \DateTime
    {
        if ($amount === 0) {
            return $date;
        }

        $amount = ceil($amount);
        $date = Carbon::instance($date);
        $sign = self::determineSign($amount);
        $day = $date->dayOfWeek;
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

        $dt = $date->toDateTime();

        if (count(self::$holidays) > 0 && self::isHoliday($date)) {
            return self::addWorkDays($dt, 1);
        }

        return $dt;
    }

    public static function subtractWorkDays(\DateTime $date, float $amount): \DateTime
    {
        return self::addWorkDays($date, -$amount);
    }

    public static function determineSign(float $number): int
    {
        return $number <=> 0;
    }

    public static function getHolidays(): array
    {
        return self::$holidays;
    }

    public static function isHoliday(\DateTime $date): bool
    {
        return self::binarySearch($date, self::$holidays) >= 0;
    }

    public static function setHolidays(array $holidays): void
    {
        self::$holidays = self::sortDateArray($holidays);
    }

    public static function sortDateArray(array $arr): array
    {
        usort($arr, function ($a, $b) {
            return $a <=> $b;
        });

        return $arr;
    }

    public static function binarySearch($date, array $holidays): int
    {
        if (count($holidays) === 0) {
            return -1;
        }

        $key = -1;
        $low = 0;
        $high = count($holidays) - 1;

        while ($high >= $low) {
            $mid = (int)floor(($high + $low) / 2);
            $cmp = $date <=> $holidays[$mid];

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

    protected static function compareDates()
    {

    }
}
