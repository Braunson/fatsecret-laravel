<?php

namespace Tests;

use Braunson\FatSecret\TimestampFactory;

class TimestampFactoryTest extends TestCase
{
    public function testGetReturnsATimestamp()
    {
        $result = app()->make(TimestampFactory::class)->get();
        
        $this->assertEquals((string) (int) $result, $result);
        $this->assertGreaterThanOrEqual(~PHP_INT_MAX, $result);
        $this->assertLessThanOrEqual(PHP_INT_MAX, $result);
    }
}
