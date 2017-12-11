<?php
namespace Braunson\FatSecret;

use Config;
use Exception;
use App;
use Log;

class FatSecretApi
{
	/**
	 * Call the url and return the resonse
	 *
	 * @param string $requestUrl The url we want to call
	 * @param array $postString  The array of fields passed in the call
	 */
	public function getQueryResponse($requestUrl, $postString)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $requestUrl);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		curl_close($ch);

		$response = json_decode($response, true);

		$this->errorCheck($response);

		return $response;
	}

	/**
	 * Checking for any errors, if so we throw a fatal Laravel error
	 *
	 * @param array $exception
	 */
	public function errorCheck($exception)
	{
		if (isset($exception['error'])) {
			\Log::error($exception['error']['message']);
			$backtrace = debug_backtrace();
			throw new \ErrorException($exception['error']['message'], 0, $exception['error']['code'], __FILE__, $backtrace[0]['line']);
		}
	}
}
