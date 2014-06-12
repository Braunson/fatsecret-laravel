<?php namespace Braunson\FatsecretLaravel;

use Config;
use Exception;

class FatSecretAPI{
	static public $base = 'http://platform.fatsecret.com/rest/server.api?';
	static public $maxresults = 10;
    
	/* Private Data */
	private $_consumerKey;
	private $_consumerSecret;
    
	/* Constructors */	
    function __construct($consumerKey, $consumerSecret){
		$this->_consumerKey = $consumerKey;
		$this->_consumerSecret = $consumerSecret;
		return $this;
	}
	
	/* Properties */
	function GetKey(){
		return $this->_consumerKey;
	}
	
	function SetKey($consumerKey){
		$this->_consumerKey = $consumerKey;
	}

	function GetSecret(){
		return $this->_consumerSecret;
	}
	
	function SetSecret($consumerSecret){
		$this->_consumerSecret = $consumerSecret;
	}
	
	/* Public Methods */
	/* Create a new profile with a user specified ID
	* @param userID {string} Your ID for the newly created profile (set to null if you are not using your own IDs)
	* @param token {string} The token for the newly created profile is returned here
	* @param secret {string} The secret for the newly created profile is returned here
	*/
	function ProfileCreate($userID, &$token, &$secret){
		$url = static::$base . 'method=profile.create';
		
		if(!empty($userID)){
			$url = $url . '&user_id=' . $userID;
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
	
	/* Get the auth details of a profile
	* @param userID {string} Your ID for the profile
	* @param token {string} The token for the profile is returned here
	* @param secret {string} The secret for the profile is returned here
	*/
	function ProfileGetAuth($userID, &$token, &$secret){
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
	
	/* Create a new session for JavaScript API users
	* @param auth {array} Pass user_id for your own ID or the token and secret for the profile. E.G.: array(user_id=>"user_id") or array(token=>"token", secret=>"secret")
	* @param expires {int} The number of minutes before a session is expired after it is first started. Set this to 0 to never expire the session. (Set to any value less than 0 for default)
	* @param consumeWithin {int} The number of minutes to start using a session after it is first issued. (Set to any value less than 0 for default)
	* @param permittedReferrerRegex {string} A domain restriction for the session. (Set to null if you do not need this)
	* @param cookie {bool} The desired session_key format
	* @param sessionKey {string} The session key for the newly created session is returned here
	*/
	function ProfileRequestScriptSessionKey($auth, $expires, $consumeWithin, $permittedReferrerRegex, $cookie, &$sessionKey){
		$url = static::$base . 'method=profile.request_script_session_key';
		
		if(!empty($auth['user_id'])){
			$url = $url . '&user_id=' . $auth['user_id'];
		}
		
		if($expires > -1){
			$url = $url . '&expires=' . $expires;
		}

		if($consumeWithin > -1){
			$url = $url . '&consume_within=' . $consumeWithin;
		}

		if(!empty($permittedReferrerRegex)){
			$url = $url . '&permitted_referrer_regex=' . $permittedReferrerRegex;
		}

		if($cookie == true)
			$url = $url . "&cookie=true";
			
		$oauth = new OAuthBase();
		
		$normalizedUrl;
		$normalizedRequestParameters;
		
		$signature = $oauth->GenerateSignature($url, $this->_consumerKey, $this->_consumerSecret, $auth['token'], $auth['secret'], $normalizedUrl, $normalizedRequestParameters);
		
		$doc = new \SimpleXMLElement($this->GetQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature)));
				
		$this->ErrorCheck($doc);
				
		$sessionKey = $doc->session_key;
	}
	
    public function test()
    {
        return 'Success!!';
    }
    
    public function searchFoods($search_phrase){
        $url = static::$base . 'method=foods.search&max_results=' .static::$maxresults. '&search_expression=' . $search_phrase;
		
		$oauth = new OAuthBase();
		
		$normalizedUrl;
		$normalizedRequestParameters;
        
		$signature = $oauth->GenerateSignature($url, $this->_consumerKey, $this->_consumerSecret, null, null, $normalizedUrl, $normalizedRequestParameters);
		$returnXML = $this->GetQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature));
		$doc = new \SimpleXMLElement($returnXML);

		$this->ErrorCheck($doc);
        
		return $returnXML;
	}

	/* Private Methods */
	private function GetQueryResponse($requestUrl, $postString) {
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
        $response = curl_exec($ch);

        curl_close($ch);
		
		return $response;
	}
	
	private function ErrorCheck($doc){
		if($doc->getName() == 'error')
		{
			throw new Exception($doc->message); // was FatSecretException((int)$doc->code, $doc->message)
		}
	}
}

/*
class FatSecretException extends Exception{
	
    public function FatSecretException($code, $message)
    {
        parent::__construct($message, $code);
    }
}
*/

/* OAuth */
class OAuthBase {
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
	
	function GenerateSignature($url, $consumerKey, $consumerSecret, $token, $tokenSecret, &$normalizedUrl, &$normalizedRequestParameters){
		$signatureBase = $this->GenerateSignatureBase($url, $consumerKey, $token, 'POST', $this->GenerateTimeStamp(), $this->GenerateNonce(), 'HMAC-SHA1', $normalizedUrl, $normalizedRequestParameters);
        $secretKey = $this->UrlEncode($consumerSecret) . '&' . $this->UrlEncode($tokenSecret);
		return base64_encode(hash_hmac('sha1', $signatureBase, $secretKey, true));
	}
	
	private function GenerateSignatureBase($url, $consumerKey, $token, $httpMethod, $timeStamp, $nonce, $signatureType, &$normalizedUrl, &$normalizedRequestParameters){		
		$parameters = array();
		
		$elements = explode('?', $url);
		$parameters = $this->GetQueryParameters($elements[1]);
		
		$parameters[OAuthBase::$OAUTH_VERSION] = OAuthBase::$OAUTH_VERSION_NUMBER;
		$parameters[OAuthBase::$OAUTH_NONCE] = $nonce;
		$parameters[OAuthBase::$OAUTH_TIMESTAMP] = $timeStamp;
		$parameters[OAuthBase::$OAUTH_SIGNATURE_METHOD] = $signatureType;
		$parameters[OAuthBase::$OAUTH_CONSUMER_KEY] = $consumerKey;
		
		if(!empty($token)){
			$parameters[ OAuthBase::$OAUTH_TOKEN] = $token;
		}
		
		$normalizedUrl = $elements[0];
		$normalizedRequestParameters = $this->NormalizeRequestParameters($parameters);
		
		return $httpMethod . '&' . UrlEncode($normalizedUrl) . '&' . UrlEncode($normalizedRequestParameters);
	}
	
    private function GetQueryParameters($paramString) {
        $elements = explode('&',$paramString); // was split
        $result = array();
        foreach ($elements as $element)
        {
            list($key,$token) = explode('=',$element);
            if($token)
                $token = urldecode($token);
            if(!empty($result[$key]))
            {
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

    private function NormalizeRequestParameters($parameters) {
        $elements = array();
        ksort($parameters);

        foreach ($parameters as $paramName=>$paramValue) {
            array_push($elements,$this->UrlEncode($paramName).'='.$this->UrlEncode($paramValue));
        }
        return join('&',$elements);
    }
	
    private function UrlEncode($string) {
        $string = urlencode($string);
        $string = str_replace('+','%20',$string);
        $string = str_replace('!','%21',$string);
        $string = str_replace('*','%2A',$string);
        $string = str_replace('\'','%27',$string);
        $string = str_replace('(','%28',$string);
        $string = str_replace(')','%29',$string);
        return $string;
    }

	private function GenerateTimeStamp(){
		return time();
	}
	
	private function GenerateNonce(){
		return md5(uniqid());
	}
}

?>
