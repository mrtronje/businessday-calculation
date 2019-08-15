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

    public function testAddWorkDays(): void
    {
        $start = DateTime::createFromFormat('Y-m-d', '2019-01-01');
        BusinessDay::setHolidays([
            DateTime::createFromFormat('Y-m-d', '2019-01-02')
        ]);


        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-03'),
            BusinessDay::addWorkDays($start, 1)
        );

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-03'),
            BusinessDay::addWorkDays($start, 1.2)
        );

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-03'),
            BusinessDay::addWorkDays($start, 0.8)
        );

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-07'),
            BusinessDay::addWorkDays($start, 4)
        );

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d', '2019-01-08'),
            BusinessDay::addWorkDays($start, 5)
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
