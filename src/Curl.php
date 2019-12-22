<?php

namespace Braunson\FatSecret;

class Curl
{
	/**
	 * Call the url and return the response.
	 *
	 * @param string $url    The url we want to call
	 * @param array  $params The array of fields passed in the call
	 */
	public function query($url, $params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		
		curl_close($ch);
		json_decode($response, true);

		return $response;
	}
}
