<?php

namespace Braunson\FatSecret;

class OAuthBase
{
    private $consumerSecret;

    public function setSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Genates a new OAuth signature for a request.
     *
     * @param string $url        The url to be signed
     * @param string $parameters The parameters query string to be signed
     * @param string $token      The token for generating the signature with
     * @param string $secret     The secret for generating the signature with
     *
     * @return string
     */
    public function generateSignature(
        string $url,
        string $parameters,
        string $token = null,
        string $secret = null
    ) {
        $base = $this->generateSignatureBase($url, $parameters, $token);
        $key = urlencode($this->consumerSecret).'&'.urlencode($secret);
        
        return base64_encode(hash_hmac('sha1', $base, $key, true));
    }

    /**
     * Genates the signature base string.
     *
     * @param string $url        The url to be signed
     * @param string $parameters The parameters query string to be signed
     *
     * @return string
     */
    private function generateSignatureBase(
        string $url,
        string $parameters
    ) {
        return 'POST&'.urlencode($url).'&'.urlencode($parameters);
    }
}
