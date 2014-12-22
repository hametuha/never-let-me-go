<?php

namespace NeverLetMeGo\Utility;


use NeverLetMeGo\Pattern\Singleton;

/**
 * Input utility
 *
 * @package NeverLetMeGo\Utility
 */
class Input extends Singleton
{

	/**
	 * Get $_GET
	 *
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function get($name){
		return isset($_GET[$name]) ? $_GET[$name] : null;
	}

	/**
	 * Get $_POST
	 *
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function post($name){
		return isset($_POST[$name]) ? $_POST[$name] : null;
	}

	/**
	 * Get $_REQUEST
	 *
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function request($name){
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
	}

	/**
	 * Verify nonce
	 *
	 * @param string $action
	 * @param string $key Default '_wpnonce'
	 *
	 * @return bool
	 */
	public function verify_nonce($action, $key = '_wpnonce'){
		return wp_verify_nonce($this->request($key), $action);
	}
}
