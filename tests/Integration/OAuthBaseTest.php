<?php

namespace Tests;

use Braunson\FatSecret\OAuthBase;

class OAuthBaseTest extends TestCase
{
    public function testGetReturnsATimestamp()
    {
        $oauth = app()->make(OAuthBase::class);
        $oauth->setSecret('consumerSecret');
        $url = 'url';
        $parameters = 'param1=value1&param2=value2';
        $token = 'token';
        $secret = 'secret';

        $result = $oauth->generateSignature($url, $parameters, $token, $secret);

        $this->assertEquals('029CvluM0Ej5sQ4h5yq7RioE8B4=', $result);
    }
}
