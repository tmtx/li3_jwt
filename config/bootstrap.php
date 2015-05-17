<?php

use lithium\action\Dispatcher;
use lithium\storage\Session;

/**
 * Set the token header in the response.
 */
Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
	$controller = $chain->next($self, $params, $chain);

	$configs = Session::config();
	foreach ($configs as $name => $config) {
		if ($config['adapter'] == 'Token') {
			$header = $config['header'];
			break;
		}
	}

	if (isset($header)) {
		$controller->response->headers($header, Session::key($name));
	}

	return $controller;
});
