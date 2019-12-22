<?php
namespace Tests;
use Braunson\FatSecret\Curl;
use Braunson\FatSecret\FatSecretApi;
use Braunson\FatSecret\UrlBuilder;
use Mockery;
class FatSecretApiTest extends TestCase
{
    protected $urlBuilder;
    protected $api;
    protected $curl;
    public function setUp()
    {
        parent::setUp();
        $this->urlBuilder = Mockery::mock(UrlBuilder::class);
        app()->instance(UrlBuilder::class, $this->urlBuilder);
        $this->curl = Mockery::mock(Curl::class);
        app()->instance(Curl::class, $this->curl);
        $this->methodParams = ['param1' => 'value1', 'param2' => 'value2'];
        $this->api = app()->make(FatSecretApi::class);
    }
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }
    private function prepareApiCall()
    {
        $this->urlBuilder->shouldReceive('setMethod')
        ->once()
        ->with('methodName')
        ->andReturn($this->urlBuilder);
        $this->urlBuilder->shouldReceive('setTimestamp')
        ->once()
        ->with()
        ->andReturn($this->urlBuilder);
        $this->urlBuilder->shouldReceive('setNonce')
        ->once()
        ->with()
        ->andReturn($this->urlBuilder);
        $this->urlBuilder->shouldReceive('setMethodParameters')
        ->once()
        ->with($this->methodParams)
        ->andReturn($this->urlBuilder);
        $this->urlBuilder->shouldReceive('sign')
        ->once()
        ->with('token', 'secret')
        ->andReturn($this->urlBuilder);
    }
    public function testExecutingAnApiMehod()
    {
        $this->prepareApiCall();
        $this->urlBuilder->shouldReceive('getBase')
        ->once()
        ->with()
        ->andReturn('urlBase');
        $this->urlBuilder->shouldReceive('getParams')
        ->once()
        ->with()
        ->andReturn($this->methodParams);
        $this->curl->shouldReceive('query')
        ->once()
        ->with('urlBase', 'param1=value1&param2=value2')
        ->andReturn(json_encode(['curl' => 'result']));
        $result = $this->api->executeMethod(
        'methodName',
        $this->methodParams,
        'token',
        'secret'
      );
        $this->assertEquals((object) ['curl' => 'result'], $result);
    }
    /**
     * @expectedException         Braunson\FatSecret\FatSecretException
     * @expectedExceptionCode     99
     * @expectedExceptionMesssage errorMessage
     */
    public function testAnErrorApiCallRaisesAnException()
    {
        $this->prepareApiCall();
        $this->urlBuilder->shouldReceive('getBase')
        ->once()
        ->with()
        ->andReturn('urlBase');
        $this->urlBuilder->shouldReceive('getParams')
        ->once()
        ->with()
        ->andReturn($this->methodParams);
        $this->curl->shouldReceive('query')
        ->once()
        ->with('urlBase', 'param1=value1&param2=value2')
        ->andReturn(json_encode(
          ['error' => ['message' => 'errorMessage', 'code' => '99']])
        );
        $result = $this->api->executeMethod(
        'methodName',
        $this->methodParams,
        'token',
        'secret'
      );
    }
}
