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
            $oauth = new OAuthBase();

            $oauth->setSecret(config('fatsecret.secret'));

            $urlBuilder = new UrlBuilder(
                $oauth,
                new NonceFactory(),
                new TimestampFactory()
            );

            $urlBuilder->setKey(config('fatsecret.key'));

            return new FatSecret(
                new FatSecretApi(
                    $urlBuilder,
                    new Curl()
                )
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
