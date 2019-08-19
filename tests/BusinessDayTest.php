<?php

use Bdc\BusinessDay;
use PHPUnit\Framework\TestCase;

class BusinessDayTest extends TestCase
{
    public function testWeekDaysBetween(): void
    {

        //Tuesday
        $start = DateTime::createFromFormat('Y-m-d', '2019-01-01');
        $end = DateTime::createFromFormat('Y-m-d', '2019-02-01');

        $this->assertEquals(
            23,
            BusinessDay::workDaysBetween($start, $end)
        );

        $end = DateTime::createFromFormat('Y-m-d', '2019-01-02');
        $this->assertEquals(
            1,
            BusinessDay::workDaysBetween($start, $end)
        );

        $end = DateTime::createFromFormat('Y-m-d', '2019-01-01');
        $this->assertEquals(
            0,
            BusinessDay::workDaysBetween($start, $end)
        );

        $end = DateTime::createFromFormat('Y-m-d', '2019-01-06');
        $this->assertEquals(
            5,
            BusinessDay::workDaysBetween($start, $end)
        );
    }

    public function testFreeDaysBetween(): void
    {
        $start = DateTime::createFromFormat('Y-m-d', '2019-01-01');

        BusinessDay::setWorkweek([1, 1, 1, 1, 1, 0, 0]);
        $end = DateTime::createFromFormat('Y-m-d', '2019-02-01');
        $this->assertEquals(
            8,
            BusinessDay::freeDaysBetween($start, $end)
        );

        $end = DateTime::createFromFormat('Y-m-d', '2019-01-18');
        $this->assertEquals(
            4,
            BusinessDay::freeDaysBetween($start, $end)
        );

        $end = DateTime::createFromFormat('Y-m-d', '2019-01-21');
        $this->assertEquals(
            6,
            BusinessDay::freeDaysBetween($start, $end)
        );

        BusinessDay::setWorkweek([1, 1, 1, 1, 0, 0, 0]);
        $end = DateTime::createFromFormat('Y-m-d', '2019-02-01');
        $this->assertEquals(
            13,
            BusinessDay::freeDaysBetween($start, $end)
        );

        $end = DateTime::createFromFormat('Y-m-d', '2019-01-18');
        $this->assertEquals(
            7,
            BusinessDay::freeDaysBetween($start, $end)
        );

        $end = DateTime::createFromFormat('Y-m-d', '2019-01-21');
        $this->assertEquals(
            9,
            BusinessDay::freeDaysBetween($start, $end)
        );

    }

    public function testAddWorkDays(): void
    {
        $start = DateTime::createFromFormat('Y-m-d', '2019-01-01');

        BusinessDay::setWorkweek([0, 1, 1, 1, 1, 0, 0]);

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-17'),
            BusinessDay::addWorkDays($start, 10)
        );

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-07'),
            BusinessDay::addWorkDays($start, 3)
        );

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-15'),
            BusinessDay::addWorkDays($start, 8)
        );

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-02-05'),
            BusinessDay::addWorkDays($start, 20)
        );


        BusinessDay::setWorkweek([0, 1, 1, 1, 1, 1, 0]);


        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-11'),
            BusinessDay::addWorkDays($start, 8)
        );
        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-29'),
            BusinessDay::addWorkDays($start, 20)
        );


        BusinessDay::setHolidays([
            DateTime::createFromFormat('Y-m-d', '2019-01-02'),
            DateTime::createFromFormat('Y-m-d', '2019-01-30')
        ]);
        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-31'),
            BusinessDay::addWorkDays($start, 20)
        );
    }

    public function testSortDateArray(): void
    {
        $holidays = [
            DateTime::createFromFormat('Y-m-d', '2019-02-07'),
            DateTime::createFromFormat('Y-m-d', '2019-04-07'),
            DateTime::createFromFormat('Y-m-d', '2019-01-07'),
            DateTime::createFromFormat('Y-m-d', '2019-03-07'),
        ];

        $sortedArray = [
            DateTime::createFromFormat('Y-m-d', '2019-01-07'),
            DateTime::createFromFormat('Y-m-d', '2019-02-07'),
            DateTime::createFromFormat('Y-m-d', '2019-03-07'),
            DateTime::createFromFormat('Y-m-d', '2019-04-07'),
        ];

        $this->assertEquals(
            $sortedArray,
            BusinessDay::sortDateArray($holidays)
        );
    }
}
