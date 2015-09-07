<?php namespace spitfire;

use App;
use spitfire\core\Response;
use spitfire\core\Request;
use spitfire\exceptions\ExceptionHandler;
use spitfire\exceptions\PrivateException;

if (!defined('APP_DIRECTORY')){
	define ('APP_DIRECTORY',         'bin/apps/',        true);
	define ('CONFIG_DIRECTORY',      'bin/settings/',    true);
	define ('ASSET_DIRECTORY',       'assets/',          true);
	define ('CONTROLLERS_DIRECTORY', 'bin/controllers/', true);
	define ('TEMPLATES_DIRECTORY',   'bin/templates/',   true);
}

/**
 * Dispatcher class of Spitfire. Calls all the required classes for Spitfire to run.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @package spitfire
 */
class SpitFire extends App
{
	
	static  $started = false;
	
	private $cwd;
	private $request;
	private $debug;
	private $apps = Array();
	
	public function __construct() {
		#Check if SF is running
		if (self::$started) { throw new PrivateException('Spitfire is already running'); }
		
		#Set the current working directory
		$this->cwd = dirname(dirname(__FILE__));
		
		#Import the exception handler for logging
		$this->debug = ExceptionHandler::getInstance();
		
		#Call parent
		parent::__construct('bin/', null);

		self::$started = true;
	}
	
	public function prepare() {

		#Try to include the user's evironment & routes
		self::includeIfPossible(CONFIG_DIRECTORY . 'environments.php');
		self::includeIfPossible(CONFIG_DIRECTORY . 'routes.php');
		
		#Define the current timezone
		date_default_timezone_set(environment::get('timezone'));
                
		#Set the display errors directive to the value of debug
		ini_set("display_errors" , environment::get('debugging_mode'));
		
	}

	public function fire() {
		
		#Import the apps
		self::includeIfPossible(CONFIG_DIRECTORY . 'path_parsers.php');
		self::includeIfPossible(CONFIG_DIRECTORY . 'apps.php');
		foreach ($this->apps as $app) { $app->createRoutes(); }
		$this->createRoutes();
		
		#Get the current path...
		$request = $this->request = Request::fromServer();
		
		#If the user responded to the current route with a response we do not need 
		#to handle the request
		if (!$request->getPath() instanceof Response) {
			#Start debugging output
			ob_start();

			#If the request has no defined controller, action and object it will define
			#those now.
			$path    = $request->getPath();
			
			#Receive the initial context for the app. The controller can replace this later
			/*@var $initContext Context*/
			$initContext = ($path instanceof Context)? $path : $request->makeContext();
			
			#Define the context for the helper function lang()
			lang(current_context($initContext));
			
			#Get the return context
			/*@var $context Context*/
			$context = $initContext->run();

			#End debugging output
			$context->view->set('_SF_DEBUG_OUTPUT', ob_get_clean());

			#Send the response
			$context->response->send();
		}
		else {
			$request->getPath()->send();
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

	/**
	 * Returns the directory the assets are located in. This function should be 
	 * avoided in favor of the ASSET_DIRECTORY constant.
	 * 
	 * Please note that the fact that this function provides a bit more flexibility
	 * than a constant would also allow users to create erratic patterns.
	 * 
	 * @deprecated since version 0.1-dev 20150423
	 */
	public function getAssetsDirectory() {
		return ASSET_DIRECTORY;
	}


	/**
	 * Returns the directory the templates are located in. This function should be 
	 * avoided in favor of the TEMPLATE_DIRECTORY constant.
	 * 
	 * @deprecated since version 0.1-dev 20150423
	 */
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

}