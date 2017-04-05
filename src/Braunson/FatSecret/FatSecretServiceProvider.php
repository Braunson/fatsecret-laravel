<?php namespace Braunson\FatSecret;

use Illuminate\Support\ServiceProvider;

class FatSecretServiceProvider extends ServiceProvider
{
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
		// $this->package('braunson/fat-secret');
		$this->register();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['fatsecret'] = $this->app->share(function($app)
		{
			return new FatSecret(\Config::get('services.fatsecret.key'), \Config::get('services.fatsecret.secret'));
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('fatsecret');
	}

}
