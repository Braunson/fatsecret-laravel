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

	function GenerateSignature($url, $consumerKey, $consumerSecret, $token, $tokenSecret, &$normalizedUrl, &$normalizedRequestParameters)
	{
		$signatureBase = $this->GenerateSignatureBase($url, $consumerKey, $token, 'POST', $this->GenerateTimeStamp(), $this->GenerateNonce(), 'HMAC-SHA1', $normalizedUrl, $normalizedRequestParameters);
		$secretKey = $this->UrlEncode($consumerSecret) . '&' . $this->UrlEncode($tokenSecret);

		return base64_encode(hash_hmac('sha1', $signatureBase, $secretKey, true));
	}

	private function GenerateSignatureBase($url, $consumerKey, $token, $httpMethod, $timeStamp, $nonce, $signatureType, &$normalizedUrl, &$normalizedRequestParameters)
	{
		$parameters = array();

		$elements = explode('?', $url);
		$parameters = $this->GetQueryParameters($elements[1]);

		$parameters[OAuthBase::$OAUTH_VERSION] = OAuthBase::$OAUTH_VERSION_NUMBER;
		$parameters[OAuthBase::$OAUTH_NONCE] = $nonce;
		$parameters[OAuthBase::$OAUTH_TIMESTAMP] = $timeStamp;
		$parameters[OAuthBase::$OAUTH_SIGNATURE_METHOD] = $signatureType;
		$parameters[OAuthBase::$OAUTH_CONSUMER_KEY] = $consumerKey;

		if (!empty($token)) {
			$parameters[ OAuthBase::$OAUTH_TOKEN] = $token;
		}

		$normalizedUrl = $elements[0];
		$normalizedRequestParameters = $this->NormalizeRequestParameters($parameters);

		return $httpMethod . '&' . UrlEncode($normalizedUrl) . '&' . UrlEncode($normalizedRequestParameters);
	}

	private function GetQueryParameters($paramString)
	{
		$elements = explode('&',$paramString); // was split
		$result   = array();

		foreach ($elements as $element) {
			list($key,$token) = explode('=',$element);
			if ($token)
				$token = urldecode($token);
			if (!empty($result[$key])) {
				if (!is_array($result[$key]))
					$result[$key] = array($result[$key],$token);
				else
					array_push($result[$key],$token);
			}
			else
				$result[$key]=$token;
		}
		return $result;
	}

	private function NormalizeRequestParameters($parameters)
	{
		$elements = array();
		ksort($parameters);

		foreach ($parameters as $paramName => $paramValue) {
			array_push($elements,$this->UrlEncode($paramName).'='.$this->UrlEncode($paramValue));
		}

		return join('&',$elements);
	}

	private function UrlEncode($string)
	{
		$string = urlencode($string);
		$string = str_replace('+','%20',$string);
		$string = str_replace('!','%21',$string);
		$string = str_replace('*','%2A',$string);
		$string = str_replace('\'','%27',$string);
		$string = str_replace('(','%28',$string);
		$string = str_replace(')','%29',$string);

		return $string;
	}

	private function GenerateTimeStamp()
	{
		return time();
	}

	private function GenerateNonce()
	{
		return md5(uniqid());
	}
}
