<?php
namespace Braunson\FatSecret;

use Config;
use Exception;
use App;
use Log;

class FatSecret
{
	static public $base = 'http://platform.fatsecret.com/rest/server.api?format=json&';

	/* Private Data */
	private $_consumerKey;
	private $_consumerSecret;

	private $api;
	private $oauth;

	/* Constructors */
	function __construct(string $consumerKey, string $consumerSecret, FatSecretApi $api, OAuthBase $oauth)
	{
		$this->_consumerKey 	= $consumerKey;
		$this->_consumerSecret 	= $consumerSecret;
		$this->api = $api;
		$this->oauth = $oauth;

		return $this;
	}

	/* Properties */

	public function getKey(){
		return $this->_consumerKey;
	}

	public function setKey($consumerKey)
	{
		$this->_consumerKey = $consumerKey;
	}

	public function getSecret()
	{
		return $this->_consumerSecret;
	}

	public function setSecret($consumerSecret)
	{
		$this->_consumerSecret = $consumerSecret;
	}

	//TODO: This is pending to be refactored
	private function generateSignatureForUrl($url, &$normalizedUrl, &$normalizedRequestParameters) {

		$normalizedUrl;
		$normalizedRequestParameters;

		return $this->oauth->generateSignature(
			$url,
			$this->_consumerKey,
			$this->_consumerSecret,
			null,
			null,
			$normalizedUrl,
			$normalizedRequestParameters
		);
	}

	/* Public Methods */

	/**
	 * Create a newsprofile with a user specified ID
	 *
	 * @param string $userID  Your ID for the newly created profile (set to null if you are not using your own IDs)
	 * @param string $token   The token for the newly created profile is returned here
	 * @param string $secret  The secret for the newly created profile is returned here
	 */
	public function profileCreate(string $userID, string &$token, string &$secret)
	{
		$url = static::$base . 'method=profile.create';

		if(!empty($userID)){
			$url = $url . 'user_id=' . $userID;
		}

		$signature = $this->generateSignatureForUrl(
			$url,
			$normalizedUrl,
			$normalizedRequestParameters
		);

		$doc = new \SimpleXMLElement(
			$this->api->getQueryResponse(
				$normalizedUrl,
				$normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature))
		);

		$this->api->errorCheck($doc);

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
	function profileGetAuth($userID, &$token, &$secret)
	{
		$url = static::$base . 'method=profile.get_auth&user_id=' . $userID;

		$signature = $this->generateSignatureForUrl(
			$url,
			$normalizedUrl,
			$normalizedRequestParameters
		);

		$doc = new \SimpleXMLElement($this->api->getQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature)));

		$this->api->errorCheck($doc);

		$token = $doc->auth_token;
		$secret = $doc->auth_secret;
	}

	//TODO: Will check this function later
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

		$normalizedUrl;
		$normalizedRequestParameters;

		$signature = $this->oauth->generateSignature($url, $this->_consumerKey, $this->_consumerSecret, $auth['token'], $auth['secret'], $normalizedUrl, $normalizedRequestParameters);

		$doc = new \SimpleXMLElement($this->getQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature)));

		$this->errorCheck($doc);

		$sessionKey = $doc->session_key;
	}

	/**
	 * Search ingredients by phrase, page and max results
	 *
	 * @param  string  $searchPhrase The phrase you want to search for
	 * @param  integer $page          The page number of results you want to return (default 0)
	 * @param  integer $maxResults    The number of results you want returned (default 50)
	 * @return json
	 */
	public function searchIngredients(string $searchPhrase, int $page = 0, int $maxResults = 50)
	{
		$url = static::$base . 'method=foods.search&page_number=' . $page . '&max_results=' . $maxResults . '&search_expression=' . $searchPhrase;

		$signature = $this->generateSignatureForUrl(
			$url,
			$normalizedUrl,
			$normalizedRequestParameters
		);

		$response = $this->api->getQueryResponse(
			$normalizedUrl,
			$normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature)
		);

		return $response;
	}

	/**
	 * Retrieve an ingredient by ID
	 *
	 * @param  integer $ingredientId  The ingredient ID
	 * @return json
	 */
	function getIngredient($ingredientId)
	{
		$url = static::$base . 'method=food.get&food_id=' . $ingredientId;

		$signature = $this->generateSignatureForUrl(
			$url,
			$normalizedUrl,
			$normalizedRequestParameters
		);
		$response = $this->api->getQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature));

		return $response;
	}
}
