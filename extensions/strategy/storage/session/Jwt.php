<?php

namespace li3_jwt\extensions\strategy\storage\session;

use lithium\core\ConfigException;

/**
 * This strategy allows you to encode and decode your `Token` data using `JSON Web Tokens`.
 * You must provide a secret key, otherwise an exception is raised.
 */
class Jwt extends \lithium\core\Object {

	/**
	 * Dynamic class dependencies.
	 *
	 * @var array
	 */
	public $_classes = ['jwt' => '\JWT'];

	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	protected $_defaults = ['algorithm' => 'HS256'];

	/**
	 * Constructor.
	 *
	 * @param array $config Configuration array. You can override the default algorithm.
	 */
	public function __construct(array $config = []) {
		if (!isset($config['secret'])) {
			throw new ConfigException('Jwt strategy requires a secret key.');
		}
		parent::__construct($config + $this->_defaults);
	}

	/**
	 * Read method.
	 *
	 * @param array $token The json web token to decode and read.
	 * @param array $options Options for this method.
	 * @return mixed Returns the decoded key or the dataset.
	 */
	public function read($token, array $options = []) {
		$jwt = $this->_classes['jwt'];

		$token = $jwt::decode($token, $this->_config['secret'], [$this->_config['algorithm']]);
		$token = (array) $token;
		$key = isset($options['key']) ? $options['key'] : null;

		if ($key) {
			return isset($token[$key]) ? $token[$key] : null;
		}
		return $token;
	}

	/**
	 * Write method.
	 *
	 * @param mixed $data The data to be encoded.
	 * @param array $options Options for this method.
	 * @return string Returns the json web token.
	 */
	public function write($data, array $options = []) {
		$jwt = $this->_classes['jwt'];

		$futureData = $this->read(null, ['key' => null] + $options) ?: [];
		$futureData = [$options['key'] => $data] + $futureData;

		return empty($futureData) ? null : $jwt::encode($futureData, $this->_config['secret']);
	}

}
