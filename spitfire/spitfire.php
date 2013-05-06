<?php

namespace spitfire;

use App;
use Headers;

require_once 'spitfire/app.php';
require_once 'spitfire/core/functions.php';

/**
 * Dispatcher class of Spitfire. Calls all the required classes for Spitfire to run.
 * @author César de la Cal <cesar@magic3w.com>
 * @package spitfire
 */

class SpitFire extends App
{
	
	static  $started         = false;
	private $cwd             = false;

	private $autoload        = false;
	static $model            = false;
	
	private $request;

	private $debug           = false;
	static $headers          = false;
	
	private $apps = Array();
	
	public function __construct() {
		#Check if SF is running
		if (self::$started) throw new \privateException('Spitfire is already running');
		
		#Set the current working directory
		$this->cwd = getcwd();
		$cur_dir = $this->cwd . DIRECTORY_SEPARATOR . 'spitfire';

		#Try to start autoload
		self::includeIfPossible($cur_dir.'/class.php');
		self::includeIfPossible($cur_dir.'/autoload.php');
		$this->autoload = new AutoLoad($this);

		#Include file to define the location of core components
		self::includeIfPossible("$cur_dir/autoload_core_files.php");

		#Initialize the exception handler
		$this->debug   = new exceptions\ExceptionHandler();
		self::$headers = new Headers();

		#Try to include the user's evironment & routes
		self::includeIfPossible(CONFIG_DIRECTORY . 'environments.php');
		self::includeIfPossible(CONFIG_DIRECTORY . 'routes.php');
		self::includeIfPossible(CONFIG_DIRECTORY . 'components.php');
                
                #Set the display errors directive to the value of debug
                ini_set("display_errors" , environment::get('debugging_mode'));


		self::$started = true;
	}

	public function fire() {
		
		self::includeIfPossible(CONFIG_DIRECTORY . 'apps.php');
		#Get the current path...
		$request = $this->request = Path::getPath();

		#Start debugging output
		ob_start();
		#Create the model
		self::$model = db();
		
		#Select the app
		
		$request->getApp()->runTask($request->getController(), $request->getAction(), $request->getObject());
		
		#End debugging output
		$request->getApp()->view->set('_SF_DEBUG_OUTPUT', ob_get_clean());

		ob_start();
		$request->getApp()->view->render();
		self::$headers->send();
		ob_flush();
	}
	
	public function registerApp($app, $namespace) {
		$this->apps[$namespace] = $app;
	}
	
	public function appExists($namespace) {
		return isset($this->apps[$namespace]);
	}
	
	/**
	 * 
	 * @param string $namespace
	 * @return App
	 */
	public function getApp($namespace) {
		return isset($this->apps[$namespace]) ? $this->apps[$namespace] : $this;
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
	
	public function log($msg) {
		if ($this->debug) $this->debug->log ($msg);
	}
	
	public function getMessages() {
		return $this->debug->getMessages();
	}
	
	public function getCWD() {
		return $this->cwd;
	}

	public function getAssetsDirectory() {
		return 'assets/';
	}

	public function getTemplateDirectory() {
		return TEMPLATES_DIRECTORY;
	}

	public function enable() {
		return null;
	}
	
	/**
	 * Returns the current request.
	 * 
	 * @return Request The current request
	 */
	public function getRequest() {
		return $this->request;
	}

	public function getControllerClassName($controller) {
		if (is_array($controller)) $c = implode('\\', $controller). 'Controller';
		elseif(is_object($controller)) {print_r (\debug_backtrace());ob_flush();die();}
		else $c = $controller . 'Controller';
		return $c;
	}

	public function hasController($controller) {
		if (empty($controller)) return false;
		return class_exists($this->getControllerClassName($controller));
	}

	public function getViewClassName($controller) {
		return implode('\\', $controller) . 'View';
	}

	public function getLocaleClassName($locale) {
		return "{$locale}Locale";
	}

}