<?php
namespace Braunson\FatSecret;

class OAuthBase
{
	/* OAuth Parameters */

	static public $OAUTH_VERSION_NUMBER = '1.0';
	static public $OAUTH_PARAMETER_PREFIX = 'oauth_';
	static public $XOAUTH_PARAMETER_PREFIX = 'xoauth_';
	static public $PEN_SOCIAL_PARAMETER_PREFIX = 'opensocial_';

	static public $OAUTH_CONSUMER_KEY = 'oauth_consumer_key';
	static public $OAUTH_CALLBACK = 'oauth_callback';
	static public $OAUTH_VERSION = 'oauth_version';
	static public $OAUTH_SIGNATURE_METHOD = 'oauth_signature_method';
	static public $OAUTH_SIGNATURE = 'oauth_signature';
	static public $OAUTH_TIMESTAMP = 'oauth_timestamp';
	static public $OAUTH_NONCE = 'oauth_nonce';
	static public $OAUTH_TOKEN = 'oauth_token';
	static public $OAUTH_TOKEN_SECRET = 'oauth_token_secret';

	protected $unreservedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.~';

	function generateSignature($url, $consumerKey, $consumerSecret, $token, $tokenSecret)
	{
		$signatureBase = $this->generateSignatureBase(
			$url,
			$consumerKey,
			$token,
			time(),
			md5(uniqid()),
			'HMAC-SHA1'
		);
		$secretKey = urlencode($consumerSecret) . '&' . urlencode($tokenSecret);

		return base64_encode(
			hash_hmac('sha1', $signatureBase, $secretKey, true)
		);
	}

	private function generateSignatureBase(
		$url,
		$consumerKey,
		$token,
		$timeStamp,
		$nonce,
		$signatureType
	) {
		$baseParameters = [
			OAuthBase::$OAUTH_VERSION => OAuthBase::$OAUTH_VERSION_NUMBER,
			OAuthBase::$OAUTH_NONCE => $nonce,
			OAuthBase::$OAUTH_TIMESTAMP => $timeStamp,
			OAuthBase::$OAUTH_SIGNATURE_METHOD => $signatureType,
			OAuthBase::$OAUTH_CONSUMER_KEY => $consumerKey
		];

		$elements = explode('?', $url);
		parse_str($elements[1], $parameters);
		$parameters = array_merge($parameters, $baseParameters);

		if (!empty($token)) {
			$parameters[OAuthBase::$OAUTH_TOKEN] = $token;
		}
		ksort($parameters);
		return 'POST&' . UrlEncode($elements[0]) . '&' . http_build_query($parameters);
	}
}
