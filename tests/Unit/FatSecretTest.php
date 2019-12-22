<?php

namespace Tests;

use Mockery;
use Braunson\FatSecret\FatSecret;
use Braunson\FatSecret\FatSecretApi;

class FatSecretTest extends TestCase
{
    protected $api;

    public function setUp() : void
    {
        parent::setUp();

        $this->api = Mockery::mock(FatSecretApi::class);

        app()->instance(FatSecretApi::class, $this->api);

        $response = $this->fatsecret = app()->make(FatSecret::class);
    }

    public function tearDown() : void
    {
        parent::tearDown();

        Mockery::close();
    }

    public function testCreatingProfileWithUserId()
    {
        $userId = '1';
        $result = 'foobar';

        $this->api->shouldReceive('executeMethod')
                    ->once()
                    ->with(
                        'profile.create',
                        [
                            'user_id' => $userId,
                        ]
                    )
                    ->andReturn($result);

        $response = $this->fatsecret->profileCreate($userId);
    }

    public function testCreatingProfileWithoutUserId()
    {
        $result = 'foobar';

        $this->api->shouldReceive('executeMethod')
                    ->once()
                    ->with(
                        'profile.create',
                        [
                            'user_id' => null,
                        ]
                    )
                    ->andReturn($result);

        $response = $this->fatsecret->profileCreate();
    }

    public function testGettingTheProfileAuth()
    {
        $userId = '1';
        $result = 'foobar';

        $this->api->shouldReceive('executeMethod')
                    ->once()
                    ->with(
                        'profile.get_auth',
                        [
                            'user_id' => $userId,
                        ]
                    )
                    ->andReturn($result);

        $response = $this->fatsecret->profileGetAuth($userId);
    }

    public function testSearchingForIngredients()
    {
        $searchExpression = 'quiz';
        $page = 0;
        $maxResults = 20;
        $result = 'foobar';

        $this->api->shouldReceive('executeMethod')
                    ->once()
                    ->with(
                        'foods.search',
                        [
                            'page_number'       => $page,
                            'max_results'       => $maxResults,
                            'search_expression' => $searchExpression,
                        ]
                    )
                    ->once()
                    ->andReturn($result);

        $response = $this->fatsecret->searchIngredients($searchExpression, $page, $maxResults);

        $this->assertEquals($result, $response);
    }

    public function testSearchingForIngredientsWithDefaultPagination()
    {
        $searchExpression = 'quiz';
        $result = 'foobar';

        $this->api->shouldReceive('executeMethod')
                    ->once()
                    ->with(
                        'foods.search',
                        [
                            'page_number'       => 0,
                            'max_results'       => 50,
                            'search_expression' => $searchExpression,
                        ]
                    )
                    ->once()
                    ->andReturn($result);

        $response = $this->fatsecret->searchIngredients($searchExpression);

        $this->assertEquals($result, $response);
    }

    public function testGettingAnIgredientById()
    {
        $ingredientId = '1';
        $result = 'foobar';

        $this->api->shouldReceive('executeMethod')
                    ->once()
                    ->with(
                        'food.get',
                        [
                            'food_id' => $ingredientId,
                        ]
                    )
                    ->andReturn($result);

        $response = $this->fatsecret->getIngredient($ingredientId);

        $this->assertEquals($result, $response);
    }
}
