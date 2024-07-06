<?php

namespace Braunson\FatSecret;

class UrlBuilder
{
    private static $base = 'https://platform.fatsecret.com/rest/server.api';

    private $parameters = [
        'format'                 => 'json',
        'oauth_version'          => '1.0',
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_consumer_key'     => null,
        'oauth_nonce'            => null,
        'oauth_timestamp'        => null,
        'method'                 => null,
        'oauth_token'            => null,
        'oauth_signature'        => null,
    ];

    private $methodParameters = [];
    private $nonce;
    private $timestamp;

    public function __construct(
        OAuthBase $oauth,
        NonceFactory $nonce,
        TimestampFactory $timestamp
    ) {
        $this->oauth = $oauth;
        $this->nonce = $nonce;
        $this->timestamp = $timestamp;
    }

    /**
     * Sets the OAuth consumer key.
     *
     * @param string $consumerKey The new consumer key
     *
     * @return UrlBuilder
     */
    public function setKey(string $consumerKey)
    {
        $this->parameters['oauth_consumer_key'] = $consumerKey;

        return $this;
    }

    /**
     * Sets the method.
     *
     * @param string $method The new method
     *
     * @return UrlBuilder
     */
    public function setMethod(string $method)
    {
        $this->parameters['method'] = $method;

        return $this;
    }

    /**
     * Sets the timestamp by generating a new one.
     *
     * @return UrlBuilder
     */
    public function setTimestamp()
    {
        $this->parameters['oauth_timestamp'] = $this->timestamp->get();

        return $this;
    }

    /**
     * Sets the nonce by generating a new one.
     *
     * @return UrlBuilder
     */
    public function setNonce()
    {
        $this->parameters['oauth_nonce'] = $this->nonce->get();

        return $this;
    }

    /**
     * Sets the signature parameter by executing the signing process.
     *
     * @param string $token  The token for generating the signature with
     * @param string $secret The secret for generating the signature with
     *
     * @return UrlBuilder
     */
    public function sign(string $token = null, string $secret = null)
    {
        $parameters = $this->getParams();

        if (!empty($token)) {
            $parameters['oauth_token'] = $token;
        }

        ksort($parameters);

        $this->parameters['oauth_signature'] = $this->oauth->generateSignature(
            static::$base,
            http_build_query($parameters),
            $token,
            $secret
        );

        return $this;
    }

    /**
     * Sets the custom method parameters.
     *
     * @param array $parameters The parameters for the method
     *
     * @return UrlBuilder
     */
    public function setMethodParameters(array $parameters)
    {
        $this->parameters['oauth_signature'] = null;
        $this->methodParameters = $parameters;

        return $this;
    }

    /**
     * Gets the url base.
     *
     * @return string
     */
    public function getBase()
    {
        return static::$base;
    }

    /**
     * Gets the whole parameter set, both default and method pameters.
     *
     * @return array
     */
    public function getParams()
    {
        return array_merge($this->parameters, $this->methodParameters);
    }
}
