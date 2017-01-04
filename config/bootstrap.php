<?php

use lithium\aop\Filters;
use lithium\action\Dispatcher;
use lithium\storage\Session;

/**
 * Set the token header in the response.
 */
Filters::apply(Dispatcher::class, "run", function($params, $next) {
	$response = $next($params);

	try {
		$configs = Session::config();

		foreach ($configs as $name => $config) {
			if ($config['adapter'] == 'Token') {
				$token = Session::key($name);
				$position = strpos($token, $config['prefix']);

				if ($position || $position === false) {
					$token = $config['prefix'] . $token;
				}

				$response->headers($config['header'], $token);
				break;
			}
		}
	} catch (\Throwable $throwable) {
	}

	return $response;
});