<?php

namespace Tests;

use Mockery;

class ExampleTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testExample()
    {
        $this->assertEquals(true, true);
    }

}
