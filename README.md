# JSON Web Token Session Adapter for Lithium

## Dependencies

* PHP >= 5.4
* [firebase/php-jwt](https://github.com/firebase/php-jwt)

## Installation

### Composer

```

composer require jasonroyle/li3_jwt

```

## Enable the Libraries

Make the application aware of the libraries by adding the following to `app/config/bootstrap/libraries.php`.

```php

/**
 * Add some plugins:
 */
Libraries::add('li3_jwt');

/**
 * Load composer libraries
 */
require_once(dirname(LITHIUM_APP_PATH) . '/vendor/autoload.php');

```

## Configuration

Add the following configuration to `app/config/bootstrap/session.php` replacing `***SECRET***` with your secret string.

```php

use lithium\storage\Session;

Session::config(['default' => [
	'adapter' => 'Token',
	'header' => 'Authorization',
	'prefix' => 'Bearer ',
	'strategies' => ['Jwt' => [
		'secret' => '***SECRET***'
	]]
]]);

```
