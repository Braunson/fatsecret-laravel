FatSecret API for Laravel
============================

The FatSecret API for Laravel gives you access to the FatSecret API.

[FatSecret](http://platform.fatsecret.com/api) provides you with access to comprehensive nutrition data for many thousands of foods, not to mention a range exercises and great weight management tools for your applications.

How to Install
--------------

1.  Install the `braunson/fatsecret` package

	```shell
	$ composer require "braunson/fatsecret"
	```

2.  Update `config/app.php` to activate FatSecret package

	```php
	# Add `FatSecretServiceProvider` to the `providers` array
	'providers' => array(
		...
		'Braunson\FatSecret\FatSecretServiceProvider',
	)

	# Add the FatSecret Facade to the `aliases` array
	'aliases' => array(
		...
		'FatSecret' => 'Braunson\FatSecret\Facade',
	)
	```


Configuration
-------------

1. Go to `config/services.php` and add this in with your details in the provided array

	```php
	// API Key & Secret (http://platform.fatsecret.com)
    'fatsecret' => [
        'secret' => env('FATSECRET_SECRET'),
        'key' => env('FATSECRET_KEY'),
    ],
	```

2. Open your `.env` file and add in
	```
	FATSECRET_SECRET=YOUR-API-SECRET
	FATSECRET_KEY=YOUR-API-KEY
	```


Usage
------------------------

When you are using this package in a file, make sure to add this to the top of your file:

```php
use Fatsecret;
```

The FatSecret is available as `FatSecret`, for example:

```php
FatSecret::ProfileCreate($userID, &$token, &$secret);
```

For more information on using the FatSecret API check out the [documentation](http://platform.fatsecret.com/api/)


Reporting Bugs or Feature Requests
----------------------------------

Please report any bugs or feature requests on the github issues page for this project here:

<https://github.com/braunson/fatsecret/issues>


Contributing
------------

-   [Fork](https://help.github.com/articles/fork-a-repo) the [FatSecret on github](https://github.com/braunson/fatsecret)
-   Commit and push until you are happy with your contribution
-   Run the tests to make sure they all pass: `composer install && ./vendor/bin/phpunit`
-   [Make a pull request](https://help.github.com/articles/using-pull-requests)
-   Thanks!


License
-------

The FatSecret Laravel API is free software released under the MIT License.
See [LICENSE](https://github.com/braunson/fatsecret/blob/master/LICENSE) for details. This is not an official release and is released separately from FatSecret.
