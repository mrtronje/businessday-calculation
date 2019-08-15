<?php

use Bdc\NumericHelper;
use PHPUnit\Framework\TestCase;

class NumericHelperTest extends TestCase
{
    public function testBinarySearch(): void
    {
        $needle = DateTime::createFromFormat('Y-m-d', '2019-01-07');
        $holidays = [
            DateTime::createFromFormat('Y-m-d', '2019-01-07'),
            DateTime::createFromFormat('Y-m-d', '2019-02-07'),
            DateTime::createFromFormat('Y-m-d', '2019-03-07'),
            DateTime::createFromFormat('Y-m-d', '2019-04-07'),
        ];

        $this->assertEquals(
            0,
            NumericHelper::binarySearch($needle, $holidays)
        );

        $needle = DateTime::createFromFormat('Y-m-d', '2019-02-07');
        $this->assertEquals(
            1,
            NumericHelper::binarySearch($needle, $holidays)
        );

        $needle = DateTime::createFromFormat('Y-m-d', '2018-02-07');
        $this->assertEquals(
            -1,
            NumericHelper::binarySearch($needle, $holidays)
        );

        $needle = DateTime::createFromFormat('Y-m-d', '2018-02-07');
        $this->assertEquals(
            -1,
            NumericHelper::binarySearch($needle, [])
        );

        $needle = DateTime::createFromFormat('Y-m-d', '2018-02-07');
        $this->assertEquals(
            -1,
            NumericHelper::binarySearch($needle, [DateTime::createFromFormat('Y-m-d', '2019-04-07')])
        );
    }
}
