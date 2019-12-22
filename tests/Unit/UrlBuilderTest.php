<?php
namespace Tests;
use Braunson\FatSecret\NonceFactory;
use Braunson\FatSecret\OAuthBase;
use Braunson\FatSecret\TimestampFactory;
use Braunson\FatSecret\UrlBuilder;
use Mockery;
class UrlBuilderTest extends TestCase
{
    protected $oauth;
    protected $timestamp;
    protected $nonce;
    protected $urlBuilder;
    public function setUp()
    {
        parent::setUp();
        $this->oauth = Mockery::mock(OAuthBase::class);
        app()->instance(OAuthBase::class, $this->oauth);
        $this->timestamp = Mockery::mock(TimestampFactory::class);
        app()->instance(TimestampFactory::class, $this->timestamp);
        $this->nonce = Mockery::mock(NonceFactory::class);
        app()->instance(NonceFactory::class, $this->nonce);
        $this->urlBuilder = app()->make(UrlBuilder::class);
    }
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }
    public function testSettingANewTimestamp()
    {
        $this->timestamp->shouldReceive('get')
        ->once()
        ->with()
        ->andReturn('timestamp');
        $result = $this->urlBuilder->setTimestamp()->getParams()['oauth_timestamp'];
        $this->assertEquals('timestamp', $result);
    }
    public function testSettingANewNonce()
    {
        $this->nonce->shouldReceive('get')
        ->once()
        ->with()
        ->andReturn('nonce');
        $result = $this->urlBuilder->setNonce()->getParams()['oauth_nonce'];
        $this->assertEquals('nonce', $result);
    }
    public function testSettingANewSignature()
    {
        $this->oauth->shouldReceive('generateSignature')
        ->once()
        ->with(
            'https://platform.fatsecret.com/rest/server.api',
            'format=json&oauth_signature_method=HMAC-SHA1&oauth_token=token&oauth_version=1.0',
            'token',
            'secret'
        )
        ->andReturn('signature');
        $result = $this->urlBuilder->sign('token', 'secret')->getParams()['oauth_signature'];
        $this->assertEquals('signature', $result);
    }
}
