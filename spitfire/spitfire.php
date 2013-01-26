<?php

namespace spitfire;

/**
 * Dispatcher class of Spitfire. Calls all the required classes for Spitfire to run.
 * @author César de la Cal <cesar@magic3w.com>
 * @package spitfire
 */

class SpitFire 
{
	
	static $started         = false;
	static $cwd             = false;

	static $autoload        = false;
	static $controller      = false;
	static $view            = false;
	static $model           = false;
	
	/** var URL Depicts the current system url*/
	static $current_url     = false;

	static $debug           = false;
	static $headers         = false;

	public static function init() {

		if (self::$started) return false;
		self::$cwd = getcwd();
		$cur_dir = self::$cwd . DIRECTORY_SEPARATOR . 'spitfire';

		#Try to start autoload
		if (! class_exists('_SF_AutoLoad')) include dirname(__FILE__).'/autoload.php';
		self::$autoload = new _SF_AutoLoad();

		#Include file to define the location of core components
		self::includeIfPossible("$cur_dir/autoload_core_files.php");

		#Initialize the exception handler
		self::$debug   = new _SF_ExceptionHandler();
		self::$headers = new Headers();

		#Try to include the user's evironment & routes
		self::includeIfPossible(CONFIG_DIRECTORY . 'environments.php');
		self::includeIfPossible(CONFIG_DIRECTORY . 'routes.php');
		self::includeIfPossible(CONFIG_DIRECTORY . 'components.php');

		#Get the current path...
		self::$current_url = _SF_Path::getPath();

		self::$started = true;
		return true;
	}

	public static function fire() {

		#Start debugging output
		ob_start();
		#Import and instance the controller
		$classBase   = implode('_', self::$current_url->getController());
		$_controller = $classBase .'Controller';
		if (!class_exists($_controller)) throw new publicException("Page not found", 404);
		self::$controller = $controller = new $_controller();
		#Create the view
		$_view      = $classBase . 'View';
		if (class_exists($_view)) self::$view = new $_view;
		else                      self::$view = new View();
		#Create the model
		self::$model = new DBInterface();
		#Check if the action is available
		$method = Array($controller, self::$current_url->getAction());
		
		#Onload
		if (method_exists($controller, 'onload') ) 
			call_user_func_array(Array($controller, 'onload'), Array(self::$current_url->getAction()));
		#Fire!
		if (is_callable($method)) call_user_func_array($method, self::$current_url->getObject());
		else throw new publicException('E_PAGE_NOT_FOUND', 404);
		#End debugging output
		self::$view->set('_SF_DEBUG_OUTPUT', ob_get_clean());

		ob_start();
		self::$view->render();
		self::$headers->send();
		ob_flush();
	}
	
	public static function baseUrl(){
		if (environment::get('base_url')) return environment::get('base_url');
		list($base_url) = explode('/index.php', $_SERVER['PHP_SELF'], 2);
		return $base_url;
	}

	public static function includeIfPossible($file) {
		if (file_exists($file)) return include $file;
		else return false;
	}

}

//TODO: Move this from here
/**
 * Returns HTML escaped string and if desired it adds ellipsis.
 * @param String $str
 * @param int $maxlength
 * @return String
 */
function __($str, $maxlength = false) {
	$str = htmlentities($str, ENT_HTML5, environment::get('system_encoding'));
	if ($maxlength) return Strings::ellipsis ($str, $maxlength);
	else return $str;
}