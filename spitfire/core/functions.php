<?php

use spitfire\SpitFire;
use spitfire\environment;
use spitfire\core\Context;
use spitfire\locales\langInfo;
use spitfire\validation\Validator;

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
 * @return \spitfire\storage\database\DB
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
	#Check if the string is numeric to return a formatted number
	if (is_numeric($str)) return number_format ($str, 2);
	if ($maxlength) $str = Strings::ellipsis ($str, $maxlength);
	
	if (defined('ENT_HTML5')) 
		$str = htmlspecialchars($str, ENT_HTML5, environment::get('system_encoding'));
	else
		$str = htmlspecialchars($str, ENT_COMPAT, environment::get('system_encoding'));
	
	return $str;
}

function _c($amt) {
	$lang = lang();
	return __($lang->convertCurrency($amt)) . $lang->getCurrency();
}

/**
 * This function retrieves the best locale based on the system confirguration or 
 * 
 * @return Locale
 */
function find_locale() {
	$context = current_context();
	try {
		if(environment::get('system_language') && $context)
			return $context->app->getLocale(environment::get('system_language'));
	}
	catch (Exception $e) {/*Ignore*/}

	$langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

	foreach($langs as $l) {
		$l = new langInfo($l);
		if (null != $c = $l->getLocaleClass($context))	return $c;
	}
	return new \spitfire\locale\sys\En;
}

/**
 * Returns the current system language.
 * 
 * @staticvar Locale $lang
 * @param string|Context $set Used to change the system language. Otherwise it will be
 *               default-ed to Accept-Language header
 * @return Locale The locale being used in the application. This allows to localize
 *               your applications with quite ease.
 */
function lang($set = null) {
	/*@var $lang Locale*/
	static $lang = null;
	
	/*@var $context Context*/
	static $context = null;
	
	# If we have chosen one retrieve it 
	if ($set instanceof \spitfire\core\Context) {
		$context = $set;
		return;
	}
	
	# If we have chosen one retrieve it 
	if ($set !== null) {
		$lang = $context->app->getLocale($set);
	}
	
	#Else try to set one
	if ($lang == null) {
		$lang = find_locale();
	}
	
	return $lang;
}

/**
 * Translates a string to the current locale. Use lang() to choose a locale, 
 * otherwise the system will try to guess the best locale by using the 
 * ACCEPT_LANG header sent by the browser.
 * 
 * @return string The translated string.
 */
function _t() {
	return call_user_func_array(Array(lang(), 'say'), func_get_args());
}

function current_context(Context$set = null) {
	static $context = null;
	if ($set!==null) {$context = $set;}
	return $context;
}

function validate($target = null) {
	if ($target !== null && $target instanceof \spitfire\validation\ValidatorInterface) {
		$targets = func_get_args();
		foreach ($targets as $target) {
			$target->validate();
		}
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
