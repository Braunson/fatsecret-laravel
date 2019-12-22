<?php
namespace Braunson\FatSecret;
class FatSecret
{
    private $api;
    public function __construct(FatSecretApi $api)
    {
        $this->api = $api;
        return $this;
    }
    /**
     * Create a newsprofile with a user specified ID.
     *
     * @param string $userId Your ID for the newly created profile (set to null if you are not using your own IDs)
     *
     * @return json
     */
    public function profileCreate(string $userId = null)
    {
        return $this->api->executeMethod('profile.create', [
            'user_id' => $userId ?: $userId,
        ]);
    }
    /**
     * Get the auth details of a profile.
     *
     * @param string $userId Your id for the profile
     *
     * @return json
     */
    public function profileGetAuth(string $userId)
    {
        return $this->api->executeMethod('profile.get_auth', [
            'user_id' => $userId,
        ]);
    }
    /**
     * Create a new session for JavaScript API users.
     *
     * @param array  $auth                   Pass user_id for your own ID or the token and secret for the profile. E.G.: array(user_id=>"user_id")
     *                                       or array(token=>"token", secret=>"secret")
     * @param int    $expires                The number of minutes before a session is expired after it is first started. Set this to 0 to never
     *                                       expire the session. (Set to any value less than 0 for default)
     * @param int    $consumeWithin          The number of minutes to start using a session after it is first issued. (Set to any value less than
     *                                       0 for default)
     * @param string $permittedReferrerRegex A domain restriction for the session. (Set to null if you do not need this)
     * @param bool   $cookie                 The desired session_key format
     *
     * @return json
     */
    public function profileRequestScriptSessionKey(array $auth, int $expires, int $consumeWithin, string $permittedReferrerRegex = null, bool $cookie)
    {
        return $this->api->executeMethod(
            'profile.request_script_session_key',
            [
                'user_id'                  => isset($auth['user_id']) ? $auth['user_id'] : null,
                'expires'                  => $expires < 0 ? $expires : null,
                'consumeWithin'            => $consumeWithin < 0 ?: $consumeWithin,
                'permitted_referred_regex' => $permittedReferrerRegex ?: null,
                'cookie'                   => $cookie === false ? null : 'true',
            ]
        );
    }
    /**
     * Search ingredients by phrase, page and max results.
     *
     * @param string $searchPhrase The phrase you want to search for
     * @param int    $page         The page number of results you want to return (default 0)
     * @param int    $maxResults   The number of results you want returned (default 50)
     *
     * @return json
     */
    public function searchIngredients(string $searchPhrase, int $page = 0, int $maxResults = 50)
    {
        return $this->api->executeMethod('foods.search', [
            'page_number'       => $page,
            'max_results'       => $maxResults,
            'search_expression' => $searchPhrase,
        ]);
    }
    /**
     * Retrieve an ingredient by ID.
     *
     * @param int $ingredientId The ingredient ID
     *
     * @return json
     */
    public function getIngredient($ingredientId)
    {
        return $this->api->executeMethod('food.get', [
            'food_id' => $ingredientId,
        ]);
    }
}
