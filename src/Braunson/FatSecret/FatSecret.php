<?php
namespace Braunson\FatSecret;

use Config, Exception, App, Log;

class FatSecret
{
	static public $base = 'http://platform.fatsecret.com/rest/server.api?format=json&';

	/* Private Data */

	private $_consumerKey;
	private $_consumerSecret;

	/* Constructors */

	function __construct($consumerKey, $consumerSecret)
	{
		$this->_consumerKey 	= $consumerKey;
		$this->_consumerSecret 	= $consumerSecret;

		return $this;
	}

	/* Properties */

	function GetKey(){
		return $this->_consumerKey;
	}

	function SetKey($consumerKey)
	{
		$this->_consumerKey = $consumerKey;
	}

	function GetSecret()
	{
		return $this->_consumerSecret;
	}

	function SetSecret($consumerSecret)
	{
		$this->_consumerSecret = $consumerSecret;
	}

	/* Public Methods */

	/**
	 * Create a new profile with a user specified ID
	 *
	 * @param string $userID  Your ID for the newly created profile (set to null if you are not using your own IDs)
	 * @param string $token   The token for the newly created profile is returned here
	 * @param string $secret  The secret for the newly created profile is returned here
	 */
	function ProfileCreate($userID, &$token, &$secret)
	{
		$url = static::$base . 'method=profile.create';

		if(!empty($userID)){
			$url = $url . 'user_id=' . $userID;
		}

		$oauth = new OAuthBase();

		$normalizedUrl;
		$normalizedRequestParameters;

		$signature = $oauth->GenerateSignature($url, $this->_consumerKey, $this->_consumerSecret, null, null, $normalizedUrl, $normalizedRequestParameters);

		$doc = new \SimpleXMLElement($this->GetQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature)));

		$this->ErrorCheck($doc);

		$token = $doc->auth_token;
		$secret = $doc->auth_secret;
	}

	/**
	 * Get the auth details of a profile
	 *
	 * @param string $userID  Your ID for the profile
	 * @param string $token   The token for the profile is returned here
	 * @param string $secret  The secret for the profile is returned here
	 */
	function ProfileGetAuth($userID, &$token, &$secret)
	{
		$url = static::$base . 'method=profile.get_auth&user_id=' . $userID;

		$oauth = new OAuthBase();

		$normalizedUrl;
		$normalizedRequestParameters;

		$signature = $oauth->GenerateSignature($url, $this->_consumerKey, $this->_consumerSecret, null, null, $normalizedUrl, $normalizedRequestParameters);

		$doc = new \SimpleXMLElement($this->GetQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature)));

		$this->ErrorCheck($doc);

		$token = $doc->auth_token;
		$secret = $doc->auth_secret;
	}

	/**
	 * 	Create a new session for JavaScript API users
	 *
	 * @param array 	$auth                   	Pass user_id for your own ID or the token and secret for the profile. E.G.: array(user_id=>"user_id")
	 *                                        		or array(token=>"token", secret=>"secret")
	 * @param integer 	$expires                	The number of minutes before a session is expired after it is first started. Set this to 0 to never
	 *                                          	expire the session. (Set to any value less than 0 for default)
	 * @param integer 	$consumeWithin          	The number of minutes to start using a session after it is first issued. (Set to any value less than
	 *                                          	0 for default)
	 * @param string 	$permittedReferrerRegex 	A domain restriction for the session. (Set to null if you do not need this)
	 * @param bool 		$cookie                 	The desired session_key format
	 * @param string 	$sessionKey             	The session key for the newly created session is returned here
	 */
	function ProfileRequestScriptSessionKey($auth, $expires, $consumeWithin, $permittedReferrerRegex, $cookie, &$sessionKey)
	{
		$url = static::$base . 'method=profile.request_script_session_key';

		if (!empty($auth['user_id'])) {
			$url = $url . 'user_id=' . $auth['user_id'];
		}

		if ($expires > -1) {
			$url = $url . '&expires=' . $expires;
		}

		if ($consumeWithin > -1) {
			$url = $url . '&consume_within=' . $consumeWithin;
		}

		if (!empty($permittedReferrerRegex)) {
			$url = $url . '&permitted_referrer_regex=' . $permittedReferrerRegex;
		}

		if ($cookie == true) {
			$url = $url . "&cookie=true";
		}

		$oauth = new \OAuthBase();

		$normalizedUrl;
		$normalizedRequestParameters;

		$signature = $oauth->GenerateSignature($url, $this->_consumerKey, $this->_consumerSecret, $auth['token'], $auth['secret'], $normalizedUrl, $normalizedRequestParameters);

		$doc = new \SimpleXMLElement($this->GetQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature)));

		$this->ErrorCheck($doc);

		$sessionKey = $doc->session_key;
	}

	/**
	 * Search ingredients by phrase, page and max results
	 *
	 * @param  string  $search_phrase The phrase you want to search for
	 * @param  integer $page          The page number of results you want to return (default 0)
	 * @param  integer $maxresults    The number of results you want returned (default 50)
	 * @return json
	 */
	public function searchIngredients($search_phrase, $page = 0, $maxresults = 50)
	{
		$url = static::$base . 'method=foods.search&page_number=' . $page . '&max_results=' . $maxresults . '&search_expression=' . $search_phrase;

		$oauth = new OAuthBase();

		$normalizedUrl;
		$normalizedRequestParameters;

		$signature = $oauth->GenerateSignature($url, $this->_consumerKey, $this->_consumerSecret, null, null, $normalizedUrl, $normalizedRequestParameters);
		$response = $this->GetQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature));

		return $response;
	}

	/**
	 * Reqtrieve an ingredient by ID
	 *
	 * @param  integer $ingredient_id  The ingredient ID
	 * @return json
	 */
	function getIngredient($ingredient_id)
	{
		$url = static::$base . 'method=food.get&food_id=' . $ingredient_id;

		$oauth = new OAuthBase();

		$normalizedUrl;
		$normalizedRequestParameters;

		$signature = $oauth->GenerateSignature($url, $this->_consumerKey, $this->_consumerSecret, null, null, $normalizedUrl, $normalizedRequestParameters);
		$response = $this->GetQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature));

		return $response;
	}

	/* Private Methods */

	/**
	 * Call the url and return the resonse
	 *
	 * @param string $requestUrl The url we want to call
	 * @param array $postString  The array of fields passed in the call
	 */
	private function GetQueryResponse($requestUrl, $postString)
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

		$this->ErrorCheck($response);

		return $response;
	}

	/**
	 * Checking for any errors, if so we throw a fatal Laravel error
	 *
	 * @param array $exception
	 */
	private function ErrorCheck($exception)
	{
		if (isset($exception['error'])) {
			\Log::error($exception['error']['message']);
			$backtrace = debug_backtrace();
			throw new \ErrorException($exception['error']['message'], 0, $exception['error']['code'], __FILE__, $backtrace[0]['line']);
		}
	}
}
