<?php

namespace Braunson\FatSecret;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'fatsecret');
    }

    public function boot()
    {
        $this->publishes([$this->configPath() => config_path('fatsecret.php')], 'config');
        $this->app['fatsecret'] = $this->app->share(function ($app) {
            return new FatSecret(
                config('fatsecret.key'),
                config('fatsecret.secret'),
                new FatSecretApi(),
                new UrlNormalizator(
                    new NonceFactory(),
                    new TimestampFactory()
                ),
                new OAuthBase()
            );
        });
    }

    public function provides()
    {
        return ['fatsecret'];
    }

    protected function configPath()
    {
        return __DIR__.'/../config/fatsecret.php';
    }
}
