<?php

namespace Tests;

use Braunson\FatSecret\OAuthBase;
use Mockery;
use OAuth;

class OAuthBaseTest extends TestCase
{

		protected $oauth;

    public function setUp()
    {
        parent::setUp();

				$this->oauth = app()->make(OAuthBase::class);
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testSettingAndGettingTheConsumerKey()
    {
			$url = 'http://foo.bar?test=green+and+red&test2=bar';
			$consumerKey = 'consumerKey';
			$consumerSecret = 'consumerSecret';
			$token = 'token';
			$tokenSecret = 'tokenSecret';
			$normalizedUrl = '';
			$normalizedRequestParameters = '';
			$signature = $this->oauth->generateSignature(
				$url,
				$consumerKey,
				$consumerSecret,
				$token,
				$tokenSecret,
				$normalizedUrl,
				$normalizedRequestParameters
			);
			//TODO: Currently working here
    }
}
