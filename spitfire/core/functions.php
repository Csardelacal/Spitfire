<?php

use spitfire\App;
use spitfire\core\Context;
use spitfire\environment;
use spitfire\locale\Domain;
use spitfire\locale\DomainGroup;
use spitfire\locale\Locale;
use spitfire\SpitFire;
use spitfire\storage\database\DB;
use spitfire\validation\ValidationException;
use spitfire\validation\Validator;
use spitfire\validation\ValidatorInterface;

/**
 * This is a quick hand method to use Spitfire's main App class as a singleton.
 * It allows you to quickly access many of the components the framework provides
 * to make it easier to read and maintain the code being created.
 * 
 * @staticvar type $sf
 * @return SpitFire
 */
function spitfire() {
	static $sf = null;
	
	if ($sf !== null) { 
		return $sf; 
	} else {
		$sf = new SpitFire();
		$sf->prepare();
		return $sf;
	}
}

/**
 * 
 * Registers a new Application in Spitfire, allowing it to handle requests directed
 * to it.
 * 
 * @param string $name The name of the Application
 * @param string $namespace The namespace in which the requests will be sent to 
 *             the application.
 * @return App The App created by the system, use this to pass parameters and 
 *             configuration to the application.
 */
function app($name, $namespace) {
	$appName = $name . 'App';
	$app = new $appName(APP_DIRECTORY . $name . DIRECTORY_SEPARATOR, $namespace);
	spitfire()->registerApp($app, $namespace);
	return $app;
}

/**
 * Shorthand function to create / retrieve the model the application is using
 * to store data. We could consider this a little DB handler factory.
 * 
 * @return DB
 */
function db($options = null) {
	static $model = null;
	
	#If we're requesting the standard driver and have it cached, we use this
	if ($options === null && $model !== null) { return $model; }
	
	#If the driver is not selected we get the one we want from env.
	if (!isset($options['db_driver'])) { $driver = environment::get('db_driver'); }
	else                               { $driver = $options['db_driver']; }
	
	#Instantiate the driver
	$driver = 'spitfire\storage\database\drivers\\' . $driver . 'Driver';
	$driver = new $driver($options);
	
	#If no options were provided we will assume that this is the standard DB handler
	if ($options === null) { $model = $driver; }
	
	#Return the driver
	return $driver;
}


/**
 * Returns HTML escaped string and if desired it adds ellipsis. If the string is
 * numeric it will reduce unnecessary decimals.
 * 
 * @param String $str
 * @param int $maxlength
 * @return String
 */
function __($str, $maxlength = false) {
	if ($maxlength) { $str = Strings::ellipsis ($str, $maxlength); }
	
	if (defined('ENT_HTML5')) 
		{ $str = htmlspecialchars($str, ENT_HTML5, environment::get('system_encoding')); }
	else
		{ $str = htmlspecialchars($str, ENT_COMPAT, environment::get('system_encoding')); }
	
	return $str;
}

/**
 * Translation helper.
 * 
 * Depending on the arguments this function receives, it will have one of several
 * behaviors.
 * 
 * If the first argument is a spitfire\locale\Locale and the function receives a
 * optional second parameter, then it will assign the locale to either the global
 * domain / the domain provided in the second parameter.
 * 
 * Otherwise, if the first parameter is a string, it will call the default locale's
 * say method. Which will translate the string using the standard locale.
 * 
 * If no parameters are provided, this function returns a DomainGroup object,
 * which provides access to the currency and date functions as well as the other
 * domains that the system has for translations.
 * 
 * @return string|DomainGroup 
 */
function _t() {
	static $domains = null;
	
	#If there are no domains we need to set them up first
	if ($domains === null) { $domains = new DomainGroup(); }
	
	#Get the functions arguments afterwards
	$args = func_get_args();
	
	#If the first parameter is a Locale, then we proceed to registering it so it'll
	#provide translations for the programs
	if ($args[0] instanceof Locale) {
		$locale = array_shift($args);
		$domain = array_shift($args);
		
		return $domains->putDomain($domain, new Domain($domain, $locale));
	}
	
	#If the args is empty, then we give return the domains that allow for printing
	#and localizing of the data.
	if (empty($args)) {
		return $domains;
	}
	
	return call_user_func_array(Array($domains->getDefault(), 'say'), $args);
}

function current_context(Context$set = null) {
	static $context = null;
	if ($set!==null) {$context = $set;}
	return $context;
}

function validate($target = null) {
	if ($target !== null && $target instanceof ValidatorInterface) {
		$targets  = func_get_args();
		$messages = Array();
		
		#Retrieve the messages from the validators
		foreach ($targets as $target) {
			$messages = array_merge($messages, $target->getMessages());
		}
		
		if (!empty($messages)) { throw new ValidationException('Validation failed', 1604200115, $messages); }
		
	} else {
		$validator = new Validator();
		$validator->setValue($target);
		return $validator;
	}
}

function get_path_info() {
	if (isset($_SERVER['PATH_INFO'])) {
		return $_SERVER['PATH_INFO'];
	} elseif (isset($_SERVER['REQUEST_URI'])) {
		$base_url = spitfire()->baseUrl();
		list($path) = explode('?', substr($_SERVER['REQUEST_URI'], strlen($base_url)));
		if (strlen($path) !== 0) { return $path; }
	}
	
	return  '/';
}

function _def(&$a, $b) {
	return ($a)? $a : $b;
}
