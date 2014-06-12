<?php namespace Braunson\FatsecretLaravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Braunson;

class FatsecretLaravelServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('braunson/fatsecret-laravel');
        
        $this->app['fatsecret'] = $this->app->share(function ($app) {
            $config = $app['config']['fatsecret-laravel'] ?: $app['config']['fatsecret-laravel::config'];
            return new FatSecretAPI($config['api_key'], $config['api_secret']);
        });
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('fatsecret-laravel');
	}

}
