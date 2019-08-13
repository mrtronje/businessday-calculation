<?php

use Bdc\BusinessDay;
use PHPUnit\Framework\TestCase;

class BusinessDayTest extends TestCase
{
    public function testWeekDaysBetween(): void
    {
        $start = DateTime::createFromFormat('Y-m-d', '2019-01-01');
        $end = DateTime::createFromFormat('Y-m-d', '2019-02-01');

        $this->assertEquals(
            BusinessDay::workDaysBetween($start, $end),
            23
        );

    }
}
