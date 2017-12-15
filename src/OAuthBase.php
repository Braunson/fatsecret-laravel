<?php
namespace Braunson\FatSecret;

class OAuthBase
{
    public function generateSignature(
        UrlNormalizator $urlNormalizator,
        string $consumerSecret,
        string $token = null,
        string $tokenSecret = null
    ) {
        $signatureBase = $this->generateSignatureBase($urlNormalizator, $token);
        $secretKey = urlencode($consumerSecret) . '&' . urlencode($tokenSecret);

        return base64_encode(hash_hmac('sha1', $signatureBase, $secretKey, true));
    }

    private function generateSignatureBase(
        UrlNormalizator $urlNormalizator,
        string $token = null
    ) {
        $urlNormalizator->generateTimestamp();
        $urlNormalizator->generateNonce();
        $parameters = $urlNormalizator->getParameters();
        if (!empty($token)) {
            $parameters['oauth_token'] = $token;
        }
        ksort($parameters);
        return 'POST&' . urlencode($urlNormalizator->getUrlBase()) . '&' . urlencode(http_build_query($parameters));
    }
}
