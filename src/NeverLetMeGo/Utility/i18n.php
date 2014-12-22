<?php

namespace NeverLetMeGo\Utility;


use NeverLetMeGo\Pattern\Singleton;

/**
 * i18n utility
 *
 * @package NeverLetMeGo\Utility
 */
class i18n extends Singleton
{

	protected $domain = 'never-let-me-go';

	/**
	 * Short hand for __()
	 *
	 * @param $str
	 *
	 * @return string|void
	 */
	public function _($str){
		return __($str, $this->domain);
	}


	/**
	 * Shorthand for _e
	 *
	 * @param $str
	 */
	public function e($str){
		echo $this->_($str);
	}

	/**
	 * Sprintf with i18n
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public function sp($str){
		$args = func_get_args();
		$args[0] = $this->_($args[0]);
		return call_user_func_array('sprintf', $args);
	}

	/**
	 * Printf with i18n
	 *
	 * @param string $str
	 */
	public function p($str){
		echo call_user_func_array(array($this, 'sp'), func_get_args());
	}
}
