<?php

namespace spitfire;

use App;
use spitfire\router\Router;

require_once 'spitfire/strings.php';
require_once 'spitfire/app.php';
require_once 'spitfire/core/functions.php';

/**
 * Dispatcher class of Spitfire. Calls all the required classes for Spitfire to run.
 * @author César de la Cal <cesar@magic3w.com>
 * @package spitfire
 */

class SpitFire extends App
{
	
	static  $started = false;
	
	private $cwd;
	private $autoload;
	private $request;
	private $debug;
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

		#Try to include the user's evironment & routes
		self::includeIfPossible(CONFIG_DIRECTORY . 'environments.php');
		self::includeIfPossible(CONFIG_DIRECTORY . 'routes.php');
		
		#If there are uploads to be handled copy them into _POST
		if(!empty($_FILES)) io\Upload::init();
		
		#Define the current timezone
		date_default_timezone_set(environment::get('timezone'));
                
		#Set the display errors directive to the value of debug
		ini_set("display_errors" , environment::get('debugging_mode'));


		self::$started = true;
	}

	public function fire() {
		
		#Get the current path...
		$path = Router::getInstance()->rewrite($_SERVER['HTTP_HOST'], $_SERVER['PATH_INFO']);
		
		#If the user responded to the current route with a response we do not need 
		#to handle the request
		if (!$path instanceof Response) {
			$request = $this->request = Request::get();

			#Import the apps
			self::includeIfPossible(CONFIG_DIRECTORY . 'path_parsers.php');
			self::includeIfPossible(CONFIG_DIRECTORY . 'apps.php');

			#Start debugging output
			ob_start();

			#If the request has no defined controller, action and object it will define
			#those now.
			$context = ($path instanceof Context)? $path : $request->makeContext($path);
			#Define te context for the helper function lang()
			lang($context);
			$context = $context->run();

			#End debugging output
			$context->view->set('_SF_DEBUG_OUTPUT', ob_get_clean());

			#Send the response
			$context->response->send();
		}
		else {
			$path->send();
		}
		
	}
	
	public function registerApp($app, $namespace) {
		$this->apps[$namespace] = $app;
	}
	
	public function appExists($namespace) {
		return isset($this->apps[$namespace]);
	}
	
	public function findAppForClass($name) {
		if (empty($this->apps)) return $this;
		
		foreach($this->apps as $app) {
			if (\Strings::startsWith($name, $app->getNameSpace())) {
				return $app;
			}
		}
		return $this;
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
		return $msg;
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
	
	public function getNameSpace() {
		return '';
	}
	
	public function getBaseDir() {
		return 'bin/';
	}

}