<?php

namespace Bdc;

use Carbon\Carbon;
use Bdc\NumericHelper;
use phpDocumentor\Reflection\Types\Self_;

class BusinessDay
{
    protected static $holidays = [];

    /**
     * Workweek scheme.
     *
     * Keys:
     * 0: Sunday
     * 1: Monday
     * 2: Tuesday
     * 3: Wednesday
     * 4: Thursday
     * 5: Friday
     * 6: Saturday
     *
     * Values:
     * 0: Non workday
     * 1: Workday
     *
     * @var array
     */
    protected static $workweek = [0, 1, 1, 1, 1, 1, 0];

    public static function workDaysBetween(\DateTime $start, \DateTime $end): int
    {
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


    public static function freeDaysBetween(\DateTime $start, \DateTime $end): int
    {
        [$start, $end] = [Carbon::instance($start), Carbon::instance($end)];

        $reverse = $end->isBefore($start);

        if ($reverse) {
            [$start, $end] = [$end, $start];
        }

        $coefficient = $reverse ? -1 : 1;

        $startDay = $start->dayOfWeek;
        $totalDays = abs($end->diffInDays($start));
        $containedDays = 0;

        for ($i = 0; $i < count(self::$workweek); $i++) {
            if (self::$workweek[$i] === 0) {
                $containedDays += NumericHelper::containedPeriodicValues($startDay, $totalDays + $startDay, $i, 7);

            }
        }
        return $containedDays;
    }

    public static function addWorkDays(\DateTime $date, int $amount): \DateTime
    {
        $date = Carbon::instance($date);

        while ($amount > 0) {
            $date->addDays(1);
            if(!self::isFreeDay($date)){
                $amount--;
            }
        }

        return $date->toDateTime();
    }

    public static function subtractWorkDays(\DateTime $date, float $amount): \DateTime
    {
        return self::addWorkDays($date, -$amount);
    }

    public static function getHolidays(): array
    {
        return self::$holidays;
    }

    public static function isFreeDay(\DateTime $date): bool
    {
        $cbDate = Carbon::instance($date);

        return self::$workweek[$cbDate->dayOfWeek] === 0 || (count(self::$holidays) > 0 && self::isHoliday($date));
    }

    public static function isHoliday(\DateTime $date): bool
    {
        return NumericHelper::binarySearch($date, self::$holidays) >= 0;
    }

    public static function setHolidays(array $holidays): void
    {
        self::$holidays = self::sortDateArray($holidays);
    }

    public static function setWorkweek(array $workweek): void
    {
        if (count($workweek) !== 7) {
            throw new \Exception('$workweek needs 7 values');
        }

        array_filter($workweek, function ($holiday) {
            if (!is_numeric($holiday)) {
                throw new \Exception('Workday values must be numeric');
            }
        });

        self::$workweek = $workweek;
    }

    public static function sortDateArray(array $arr): array
    {
        usort($arr, function ($a, $b) {
            return $a <=> $b;
        });

        return $arr;
    }
}
