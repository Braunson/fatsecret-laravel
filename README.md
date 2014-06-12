FatSecret API for Laravel
============================

The FatSecret API for Laravel gives you access through laravel to the FatSecret API.

[FatSecret](http://platform.fatsecret.com/api) provides you with access to comprehensive nutrition data for many thousands of foods, not to mention a range exercises and great weight management tools for your applications.

How to Install
--------------

1.  Install the `braunson/fatsecret-laravel` package

    ```shell
    $ composer require "braunson/fatsecret-laravel:dev-master"
    ```

2.  Update `app/config/app.php` to activate FatSecretAPI package

    ```php
    # Add `FatsecretLaravelServiceProvider` to the `providers` array
    'providers' => array(
        ...
        'Braunson\FatsecretLaravel\FatsecretLaravelServiceProvider',
    )

    # Add the `FatsecretFacade` to the `aliases` array
    'aliases' => array(
        ...
        'Fatsecret' => 'Braunson\FatsecretLaravel\FatsecretFacade',
    )
    ```


Configuration
-------------

1.  Generate a template Fatsecret config file

    ```shell
    $ php artisan config:publish braunson/fatsecret-laravel
    ```

2.  Update `app/config/packages/braunson/fatsecret-laravel/config.php` with your
    Fatsecret API key and API secret:

    ```php
    return array(
        'api_key'    => 'YOUR-API-KEY-HERE',
        'api_secret' => 'YOUR-API-SECRET-HERE',
    );
    ```


Usage
------------------------

The FatSecretAPI is available as `Fatsecret`, for example:

```php
Fatsecret::ProfileCreate($userID, &$token, &$secret);
```

For more information on using the FatSecret API check out the [documentation](http://platform.fatsecret.com/api/)


Reporting Bugs or Feature Requests
----------------------------------

Please report any bugs or feature requests on the github issues page for this project here:

<https://github.com/braunson/fatsecret-laravel/issues>


Contributing
------------

-   [Fork](https://help.github.com/articles/fork-a-repo) the [FatsecretLaravel on github](https://github.com/braunson/fatsecret-laravel)
-   Commit and push until you are happy with your contribution
-   Run the tests to make sure they all pass: `composer install && ./vendor/bin/phpunit`
-   [Make a pull request](https://help.github.com/articles/using-pull-requests)
-   Thanks!


License
-------

The Fatsecret Laravel API is free software released under the MIT License. 
See [LICENSE](https://github.com/braunson/fatsecret-laravel/blob/master/LICENSE) for details. This is not an offical release and is released seperately from FatSecret.