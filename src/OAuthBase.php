<?php
namespace Braunson\FatSecret;

class OAuthBase
{
	static public $OAUTH_VERSION_NUMBER = '1.0';
	static public $OAUTH_CONSUMER_KEY = 'oauth_consumer_key';
	static public $OAUTH_VERSION = 'oauth_version';
	static public $OAUTH_SIGNATURE_METHOD = 'oauth_signature_method';
	static public $OAUTH_SIGNATURE = 'oauth_signature';
	static public $OAUTH_TIMESTAMP = 'oauth_timestamp';
	static public $OAUTH_NONCE = 'oauth_nonce';
	static public $OAUTH_TOKEN = 'oauth_token';

	private $nonce;
	private $timestamp;

	function __construct(
		NonceFactory $nonce,
		TimestampFactory $timestamp
	) {
		$this->nonce = $nonce;
		$this->timestamp = $timestamp;
	}

	function generateSignature(
		UrlNormalizator $urlNormalizator,
		string $consumerKey,
		string $consumerSecret,
		string $token = null,
		string $tokenSecret = null
	) {
		$signatureBase = $this->generateSignatureBase(
			$urlNormalizator,
			$consumerKey,
			$token,
			$this->timestamp->get(),
			$this->nonce->get(),
			'HMAC-SHA1'
		);
		$secretKey = urlencode($consumerSecret) . '&' . urlencode($tokenSecret);

		return base64_encode(
			hash_hmac('sha1', $signatureBase, $secretKey, true)
		);
	}

	private function generateSignatureBase(
		UrlNormalizator $urlNormalizator,
		string $consumerKey,
		string $token,
		string $timeStamp,
		string $nonce,
		string $signatureType
	) {
		$parameters = array_merge(
			$urlNormalizator->getParameters(),
			[
				OAuthBase::$OAUTH_VERSION => OAuthBase::$OAUTH_VERSION_NUMBER,
				OAuthBase::$OAUTH_NONCE => $nonce,
				OAuthBase::$OAUTH_TIMESTAMP => $timeStamp,
				OAuthBase::$OAUTH_SIGNATURE_METHOD => $signatureType,
				OAuthBase::$OAUTH_CONSUMER_KEY => $consumerKey
			]
		);
		if (!empty($token)) {
			$parameters[OAuthBase::$OAUTH_TOKEN] = $token;
		}
		ksort($parameters);
		return 'POST&' .
			UrlEncode($urlNormalizator->getUrlBase()) .
			'&' .
			http_build_query($parameters);
	}
}
