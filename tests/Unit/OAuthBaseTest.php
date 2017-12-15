<?php

namespace Tests;

use Braunson\FatSecret\OAuthBase;
use Braunson\FatSecret\UrlNormalizator;
use Braunson\FatSecret\TimestampFactory;
use Braunson\FatSecret\NonceFactory;
use Mockery;

class OAuthBaseTest extends TestCase
{

		protected $oauth;
		protected $timestamp;
		protected $nonce;

    public function setUp()
    {
        parent::setUp();

				$this->timestamp = Mockery::mock(TimestampFactory::class);
				app()->instance(TimestampFactory::class, $this->timestamp);

				$this->nonce = Mockery::mock(NonceFactory::class);
				app()->instance(NonceFactory::class, $this->nonce);

				$this->oauth = app()->make(OAuthBase::class);
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testGeneratingAsignature()
    {
			$urlNormalizatorMock = Mockery::mock(UrlNormalizator::class);
			$consumerKey = 'consumerKey';
			$consumerSecret = 'consumerSecret';
			$token = 'token';
			$tokenSecret = 'tokenSecret';
			$normalizedUrl = '';
			$normalizedRequestParameters = '';

			$urlNormalizatorMock->shouldReceive('getParameters')
				->once()
				->with()
				->andReturn([
					'param1' => 'value+1',
					'param2' => 'value+2',
					'param3' => 'value+3'
				]);
			$urlNormalizatorMock->shouldReceive('getUrlBase')
				->once()
				->with()
				->andReturn('http://foo.bar');

			$this->timestamp->shouldReceive('get')
				->once()
				->with()
				->andReturn(1513118347);

			$this->nonce->shouldReceive('get')
				->once()
				->with()
				->andReturn('1be11a504405589ad7e4786ca94d9e8c');

			$signature = $this->oauth->generateSignature(
				$urlNormalizatorMock,
				$consumerKey,
				$consumerSecret,
				$token,
				$tokenSecret
			);
			$this->assertEquals('kvdJmn3O58O4I8jR2TWWGunou7I=', $signature);
    }
}
