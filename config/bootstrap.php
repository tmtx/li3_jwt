<?php

use lithium\action\Dispatcher;
use lithium\storage\Session;

/**
 * Set the token header in the response.
 */
Dispatcher::applyFilter('run', function($self, $params, $chain) {
	$response = $chain->next($self, $params, $chain);

	$configs = Session::config();
	foreach ($configs as $name => $config) {
		if ($config['adapter'] == 'Token') {
			$header = $config['header'];
			break;
		}
	}

	if (isset($header)) {
		$response->headers($header, Session::key($name));
	}

	return $response;
});
