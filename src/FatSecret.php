<?php
namespace Braunson\FatSecret;

class FatSecret
{
	static public $base = 'http://platform.fatsecret.com/rest/server.api?format=json&';

	private $_consumerKey;
	private $_consumerSecret;
	private $api;
	private $oauth;

	function __construct(
		string $consumerKey,
		string $consumerSecret,
		FatSecretApi $api,
		UrlNormalizator $urlNormalizator,
		OAuthBase $oauth
	) {
		$this->_consumerKey 	= $consumerKey;
		$this->_consumerSecret 	= $consumerSecret;
		$this->api = $api;
		$this->oauth = $oauth;
		$this->urlNormalizator = $urlNormalizator;
		return $this;
	}

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
	private function generateSignatureForUrl($url, $token = null, $secret = null) {

		return $this->oauth->generateSignature(
			$url,
			$this->_consumerKey,
			$this->_consumerSecret,
			$token,
			$secret
		);
	}

	/* Public Methods */

	/**
	 * Create a newsprofile with a user specified ID
	 *
	 * @param string $userId  Your ID for the newly created profile (set to null if you are not using your own IDs)
	 */
	public function profileCreate(string $userId)
	{
		$url = static::$base . 'method=profile.create';

		if(!empty($userId)){
			$url = $url . 'user_id=' . $userId;
		}
		$this->urlNormalizator->setUrl($url);

		$signature = $this->generateSignatureForUrl(
			$this->urlNormalizator
		);

		$doc = new \SimpleXMLElement(
			$this->getQueryResponse($signature)
		);

		$this->api->errorCheck($doc);

		//TODO: A class for this
		return [
			'token' => $doc->auth_token,
			'secret' => $doc->auth_secret,
		];
	}

	/**
	 * Get the auth details of a profile
	 *
	 * @param string $userId Your id for the profile
	 */
	public function profileGetAuth(string $userId)
	{
		$this->urlNormalizator->setUrl(
			static::$base . 'method=profile.get_auth&user_id=' . $userId
		);

		$signature = $this->generateSignatureForUrl(
			$this->urlNormalizator
		);

		$doc = new \SimpleXMLElement(
			$this->getQueryResponse($signature)
		);

		$this->api->errorCheck($doc);

		//TODO: A class for this
		return [
			'token' => $doc->auth_token,
			'secret' => $doc->auth_secret,
		];
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
	 */
	function ProfileRequestScriptSessionKey($auth, $expires, $consumeWithin, $permittedReferrerRegex, $cookie, &$sessionKey)
	{
		$url = static::$base . 'method=profile.request_script_session_key';

		if (!empty($auth['user_id'])) {
			$url .= "user_id={$auth['user_id']}";
		}

		if ($expires > -1) {
			$url .= "&expires={$expires}";
		}

		if ($consumeWithin > -1) {
			$url .= "&consume_within={$consumeWithin}";
		}

		if (!empty($permittedReferrerRegex)) {
			$url .= "&permitted_referred_regex={$permittedReferrerRegex}";
		}

		if ($cookie === true) {
			$url .= "&cookie=true";
		}

		$this->urlNormalizator->setUrl($url);

		$signature = $this->generateSignatureForUrl(
			$this->urlNormalizator,
			$auth['token'],
			$auth['secret']
		);

		$doc = new \SimpleXMLElement(
			$this->getQueryResponse($signature)
		);

		$this->api->errorCheck($doc);

		//TODO: A class for this
		return [
			'sesionKey' => $doc->session_key
		];
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
		$this->urlNormalizator->setUrl(
			static::$base .
			'method=foods.search&page_number=' .
			$page .
			'&max_results=' .
			$maxResults .
			'&search_expression=' .
			$searchPhrase
		);

		$signature = $this->generateSignatureForUrl(
			$this->urlNormalizator
		);

		return $this->getQueryResponse($signature);
	}

	/**
	 * Retrieve an ingredient by ID
	 *
	 * @param  integer $ingredientId  The ingredient ID
	 * @return json
	 */
	public function getIngredient($ingredientId)
	{
		$this->urlNormalizator->setUrl(
			static::$base . 'method=food.get&food_id=' . $ingredientId
		);
		$signature = $this->generateSignatureForUrl(
			$this->urlNormalizator
		);
		return $this->getQueryResponse($signature);
	}

	//TODO: Document and refactor this
	private function getQueryResponse($signature) {
		return $this->api->getQueryResponse(
			$this->urlNormalizator->getUrlBase(),
			http_build_query($this->urlNormalizator->getParameters()).
			'&' .
			OAuthBase::$OAUTH_SIGNATURE .
			'=' .
			urlencode($signature)
		);
	}
}
