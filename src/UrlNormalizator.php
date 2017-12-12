<?php
namespace Braunson\FatSecret;

class UrlNormalizator
{

	private $urlBase;
	private $parameters = [];

	public function setUrl(string $url) {
		$elements = explode('?', $url);
		$this->urlBase = $elements[0];
		if (count($elements) > 1) {
			parse_str($elements[1], $this->parameters);
		}
	}

	public function getUrlBase() {
		return $this->urlBase;
	}

	public function getParameters() {
		return $this->parameters;
	}
}
