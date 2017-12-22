<?php

namespace Tests;

use Braunson\FatSecret\NonceFactory;

class NonceFactoryTest extends TestCase
{
    public function testGetReturnsAValidMd5Nonce()
    {
        $result = app()->make(NonceFactory::class)->get();
        $this->assertRegExp('/^[a-f0-9]{32}$/', $result);
    }

    public function testGetAUniqueNonce()
    {
        $factory = app()->make(NonceFactory::class);
        $result = [
            $factory->get(),
            $factory->get(),
            $factory->get(),
            $factory->get(),
            $factory->get(),
        ];
        $this->assertEquals(count($result), count(array_unique($result)));
    }
}
