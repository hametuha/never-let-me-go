<?php

namespace NeverLetMeGo\Pattern;


/**
 * Singleton pattern
 *
 * @package NeverLetMeGo\Pattern
 */
abstract class Singleton
{

	/**
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	protected function __construct( $settings = array() ){
		// Override this
	}

	/**
	 * Get instance
	 *
	 * @param array $settings
	 *
	 * @return static
	 */
	public static function getInstance( $settings = array() ){
		$class_name = get_called_class();
		if( !isset(self::$instances[$class_name]) ){
			self::$instances[$class_name] = new $class_name($settings);
		}
		return self::$instances[$class_name];
	}

}
