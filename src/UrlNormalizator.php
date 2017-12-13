<?php
namespace Braunson\FatSecret;

class UrlNormalizator
{

	private $urlBase;
	private $parameters = [
		'oauth_version' => '1.0',
		'oauth_signature_method' => 'HMAC-SHA1',
		'oauth_consumer_key' => null,
		'oauth_nonce' => null,
		'oauth_timestamp' => null
	];

	public function setUrl(string $url) {
		$elements = explode('?', $url);
		$this->urlBase = $elements[0];
		if (count($elements) > 1) {
			parse_str($elements[1], $urlParameters);
			$this->parameters = array_merge($this->parameters, $urlParameters);
		}
	}

	public function setTimestamp(int $timestamp) {
		$this->parameters['oauth_timestamp'] = $timestamp;
	}

	public function setNonce(string $nonce) {
		$this->parameters['oauth_nonce'] = $nonce;
	}

	public function setConsumerKey(string $consumerKey) {
		$this->parameters['oauth_consumer_key'] = $consumerKey;
	}

	public function getUrlBase() {
		return $this->urlBase;
	}

	public function getParameters() {
		return $this->parameters;
	}
}
