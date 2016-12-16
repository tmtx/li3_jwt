<?php

namespace li3_jwt\extensions\adapter\storage\session;

use lithium\core\ConfigException;

/**
 * A minimal adapter to interface with token sessions.
 *
 * This adapter provides basic support for `write`, `read` and `delete`
 * session handling, as well as allowing these three methods to be filtered as
 * per the Lithium filtering system.
 */
class Token extends \lithium\core\Object {

	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	protected $_defaults = ['prefix' => ''];

	/**
	 * Stores the token.
	 *
	 * @var null|string
	 */
	public static $_token = null;

	/**
	 * Constructor.
	 *
	 * @param array $config Optional configuration parameters.
	 * @return void
	 */
	public function __construct(array $config = []) {
		if (!isset($config['header'])) {
			throw new ConfigException('Token adapter requires a header.');
		}
		parent::__construct($config + $this->_defaults);
	}

	/**
	 * Checks for the token header in the request and initiates the session if the header is set.
	 *
	 * @return void
	 */
	protected function _init() {
		$header = $this->_config['header'];
		$headers = getallheaders();

		if (isset($headers[$header])) {
			$this->key($headers[$header]);
		}
	}

	/**
	 * Sets or obtains the token.
	 *
	 * @param string $token Optional. If specified, sets the token.
	 * @return mixed Token, or `null` if the session has not been started.
	 */
	public function key($token = null) {
		if (isset($token)) {
			$prefix = $this->_config['prefix'];
			$prefixPosition = strpos($token, $prefix);
			if ($prefixPosition || $prefixPosition === false) {
				$token = $prefix . $token;
			}
			static::$_token = $token;
		}
		return static::$_token;
	}

	/**
	 * Ends the session by unsetting the token.
	 *
	 * @return boolean `true` if the token is successfully unset, `false` otherwise.
	 */
	public static function end() {
		static::$_token = null;
		return !isset(static::$_token);
	}

	/**
	 * Checks if the token has been set.
	 *
	 * @return boolean `true` if the token is set, `false` otherwise.
	 */
	public static function isStarted() {
		return !!static::$_token;
	}

	/**
	 * Checks if a value has been set in the token.
	 *
	 * @param string $key Key of the entry to be checked.
	 * @return Closure Function returning boolean `true` if the key exists, `false` otherwise.
	 */
	public function check($key) {
		$config = $this->_config;
		$payload = $this->read();

		return function($params) use (&$config, $payload) {
			return isset($payload[$params['key']]);
		};
	}

	/**
	 * Read a value from the token.
	 *
	 * @param null|string $key Key of the entry to be read. If $key is `null`, returns all token
	 *                         key/value pairs that have been set.
	 * @param array $options Options array. Not used in this adapter.
	 * @return Closure Function returning data in the session if successful, `null` otherwise.
	 */
	public function read($key = null, array $options = []) {
		$config = $this->_config;
		$token = $this->key();

		return function($params) use (&$config, $token) {
			$token = str_replace($config['prefix'], '', $token);
			return $token;
		};
	}

	/**
	 * Write a value to the token.
	 *
	 * @param string $key Key of the item to be stored.
	 * @param mixed $value The value to be stored.
	 * @param array $options Options array. Not used by this adapter.
	 * @return Closure Function returning boolean `true` on successful write, `false` otherwise.
	 */
	public function write($key, $value = null, array $options = []) {
		return function($params) {
			return true;
		};
	}

	/**
	 * Delete a value from the token.
	 *
	 * @param string $key The key of the item to be deleted.
	 * @param array $options Options array. Not used by this adapter.
	 * @return Closure Function returning boolean `true` on successful delete, `false` otherwise.
	 */
	public function delete($key, array $options = []) {
		$config = $this->_config;

		return function($params) use (&$config) {
			$key = $params['key'];
			$payload = $this::read();

			if (!isset($payload[$key])) return true;

			unset($payload[$key]);
			$this::clear($params);
			foreach ($payload as $key => $value) {
				$this::write($key, $value, $params);
			}
			return true;
		};
	}

	/**
	 * Unsets the token.
	 *
	 * @param array $options Options array. Not used fro this adapter method.
	 * @return boolean `true` on successful clear, `false` otherwise.
	 */
	public function clear(array $options = []) {
		$config = $this->_config;
		$tokenClass = __CLASS__;

		return function($params) use (&$config, $tokenClass) {
			return $tokenClass::end();
		};
	}

}
