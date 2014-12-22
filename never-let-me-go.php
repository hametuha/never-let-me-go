<?php
/*
Plugin Name: Never Let Me Go
Plugin URI: http://wordpress.org/extend/plugins/never-let-me-go/
Author: Takahshi Fumiki
Version: 0.9.0
Author URI: http://takahashifumiki.com
Description: This Plugin allows your user to delete his/her own account. If you want, you can also display somehow painful thank-you message on his resignation.
Text Domain: never-let-me-go
Domain Path: /language/
*/

// Register Domain
load_plugin_textdomain('never-let-me-go', false, basename(dirname(__FILE__)).DIRECTORY_SEPARATOR."language");

// Base file name
define('NLMG_BASE_FILE', __FILE__);

// Version
define('NLMG_VERSION', '0.9.0');

if( version_compare(phpversion(), '5.3.0', '>=') ){
	spl_autoload_register('_nlmg_autoloader');
	call_user_func(array('NeverLetMeGo\Admin', 'getInstance'));
	call_user_func(array('NeverLetMeGo\Page', 'getInstance'));
}else{
	add_action('admin_notices', '_nlmg_version_notice');
}


/**
 * Auto loader
 *
 * @internal
 */
function _nlmg_autoloader($class_name){
	$class_name = ltrim($class_name, '\\');
	if( 0 === strpos($class_name, 'NeverLetMeGo\\') ){
		$path = __DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class_name).'.php';
		if( file_exists($path) ){
			require $path;
		}
	}
}

/**
 * Show version message
 *
 * @internal
 */
function _nlmg_version_notice(){
	$message = sprintf(__('<strong>Plugin Error: </strong>Never Let Me Go requires PHP 5.3 and over, but your PHP is %s. Please consider updating your PHP or downgrading this plugin to <a href="%s">0.8.2</a>.', 'never-let-me-go'),
		phpversion(), 'https://downloads.wordpress.org/plugin/never-let-me-go.0.8.2.zip');
	printf('<div class="error"><p>%s</p></div>', $message);
}


// Only for Poedit
if( false ){
	return $i18n->_('This Plugin allows your user to delete his/her own account. If you want, you can also display somehow painfull thank-you message on his resignation.');
}
