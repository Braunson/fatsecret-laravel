<?php namespace Braunson\FatSecret;

use Illuminate\Support\ServiceProvider;

class FatSecretServiceProvider extends ServiceProvider
{
	public function boot()
	{
		//
	}

	public function register()
	{
		$this->app->singleton(FatSecret::class, function () {
			return new FatSecret(config('services.fatsecret.key'), config('services.fatsecret.secret'));
		});

		$this->app->alias(FatSecret::class, 'fatsecret');
	}

	public function provides()
	{
		return array('fatsecret');
	}
}
