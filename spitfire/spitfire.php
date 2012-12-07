<?php

/**
 * Dispatcher class of Spitfire. Calls all the required classes for Spitfire to run.
 * @author César de la Cal <cesar@magic3w.com>
 * @package Spitfire
 */

class SpitFire 
{
	
	static $started         = false;

	static $autoload        = false;
	static $controller      = false;
	static $view            = false;
	static $model           = false;

	static $controller_name = false;
	static $action          = false;
	static $object          = false;
	static $extension       = false;
	
	static $current_url     = false;

	static $debug           = false;

	public static function init() {

		if (self::$started) return false;
		$cur_dir = dirname(__FILE__);

		#Try to start autoload
		if (! class_exists('_SF_AutoLoad')) include dirname(__FILE__).'/autoload.php';
		self::$autoload = new _SF_AutoLoad();

		#Include file to define the location of core components
		self::includeIfPossible("$cur_dir/autoload_core_files.php");

		#Initialize the exception handler
		self::$debug = new _SF_ExceptionHandler();

		#Try to include the user's evironment & routes
		self::includeIfPossible(CONFIG_DIRECTORY . 'environments.php');
		self::includeIfPossible(CONFIG_DIRECTORY . 'routes.php');
		self::includeIfPossible(CONFIG_DIRECTORY . 'components.php');

		#Get the current path...
		self::getPath();

		self::$started = true;
		return true;
	}

	public static function fire() {

		#Import and instance the controller
		$_controller = self::$controller_name.'Controller';
		if (!class_exists($_controller)) throw new publicException("Page not found", 404);
		self::$controller = $controller = new $_controller();
		#Create the view
		self::$view = new View(self::$controller_name, self::$action, self::$extension);
		#Create the model
		self::$model = new DBInterface();
		#Check if the action is available
		$method = Array($controller, self::$action);
		
		#Onload
		if (method_exists($controller, 'onload') ) 
			call_user_func_array(Array($controller, 'onload'), Array());
		#Fire!
		if (is_callable($method)) call_user_func_array($method, self::$object);
		else throw new publicException(E_PAGE_NOT_FOUND, 404);

		self::$view->render();
	}
	
	public static function baseUrl(){
		if (environment::get('base_url')) return environment::get('base_url');
		list($base_url) = explode('/index.php', $_SERVER['PHP_SELF'], 2);
		return $base_url;
	}


	/**
	 * getPath()
	 * Reads the current path the user has selected and tries to detect
	 * Controllers, actions and objects from it.
	 * 
	 * [NOTICE] getPath does not guarantee safe input, you will have to
	 * manually check whether the input it received is valid.
	 * 
	 * [NOTICE] getPath will prevent users from accessing any controllers
	 * when in maintenance mode. But will also throw an error when the
	 * user tries to enter maintenance mode when in normal operation.
	 * This means you need to use a separate controller for generating
	 * WiP sites, or manually edit it everytime you enter service mode.
	 */
	protected static function getPath() {
		if ( environment::get('maintenance_enabled') ) {
			define ('controller', maintenance_controller, true);
			define ('action', false, true);
			define ('object', false, true);
			return true;
		} else {

			$path_info = $_SERVER['PATH_INFO'];
			$path_info = router::rewrite($path_info);
			$path_info = substr($path_info, 1);
			$path = explode('/', $path_info);
			
			//Try to fetch the extension
			$last      = explode('.', end($path));
			$extension = (isset($last[1]))? $last[1] : false;
			
			if ($extension) {
				array_pop($path);
				array_push($path, $last[0]);
			}
			
			@list($controller, $action) = $path;
			
			//Assign the object as array (with multiple elements) to a Global
			array_shift($path); array_shift($path);
			$object = $path;
			
			//If the controller, action or object was left empty fill it with defaults 
			if (empty ($controller)) $controller = environment::get('default_controller');
			if (empty ($action)) $action = environment::get('default_action');
			if (empty ($object)) $object = environment::get('default_object');
			
			//Check if invalid url's are being requested
			if ($controller == maintenance_controller) throw new publicException('User tried to access maintenance mode', 401);
			if (substr($action, 0,1) == '_') throw new publicException(E_PAGE_NOT_FOUND, E_PAGE_NOT_FOUND_CODE);
			
			//Define controllers
			self::$controller_name = $controller;
			self::$action          = $action;
			self::$object          = $object;
			self::$extension       = $extension;
			self::$current_url     = new URL($controller, $action, $object);
			return true;

		}
	}

	public static function includeIfPossible($file) {
		if (file_exists($file)) return include $file;
		else return false;
	}

}