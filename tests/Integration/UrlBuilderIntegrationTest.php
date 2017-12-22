<?php

namespace Tests;

use Braunson\FatSecret\UrlBuilder;

class UrlBuilderIntegrationTest extends TestCase
{
    public function testBuildingAndRetrievingASignedUrl()
    {
        $url = app()->make(UrlBuilder::class)
        ->setKey('foobar')
        ->setMethod('test.method')
        ->setMethodParameters([
          'param1' => 'value1',
          'param2' => 'value2',
        ])->sign();
        $this->assertEquals(
        'https://platform.fatsecret.com/rest/server.api',
        $url->getBase()
      );
        $this->assertEquals(
        [
          'format'                 => 'json',
          'oauth_version'          => '1.0',
          'oauth_signature_method' => 'HMAC-SHA1',
          'oauth_consumer_key'     => 'foobar',
          'oauth_nonce'            => null,
          'oauth_timestamp'        => null,
          'method'                 => 'test.method',
          'oauth_token'            => null,
          'oauth_signature'        => 'p9Lb246N/C2yNN9/fg/75XqvlMs=',
          'param1'                 => 'value1',
          'param2'                 => 'value2',
        ],
        $url->getParams()
      );
    }
}
