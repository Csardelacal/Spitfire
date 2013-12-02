<?php

use spitfire\SpitFire;
use spitfire\environment;
use spitfire\Context;
use spitfire\locales\langInfo;
use spitfire\storage\database\DB;
use spitfire\validation\Validatable;
use spitfire\validation\Validator;

function spitfire() {
	static $sf;
	if ($sf) return $sf;
	
	$sf = new SpitFire();
	$sf->fire();
	return $sf;
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
 * to store data.
 * 
 * @return DB|\spitfire\storage\database\DB|spitfire.storage.database.DB
 */
function db($options = null) {
	static $model;
	
	if (is_null($options) && !empty($model)) return $model;
	
	#If the driver is not selected we get the one we want from env.
	if (!isset($options['db_driver'])) $driver = environment::get('db_driver');
	else $driver = $options['db_driver'];
	
	#Instantiate the driver
	$driver = 'spitfire\storage\database\drivers\\' . $driver . 'Driver';
	$driver = new $driver($options);
	#Store to main model
	if (is_null($options)) $model = $driver;
	return $driver;
}


/**
 * Returns HTML escaped string and if desired it adds ellipsis. If the string is
 * numeric it will reduce unnecessary decimals.
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
		if ($l->isUnderstood())	return $l->getLocaleClass($context);
	}
	return new \spitfire\locale\sys\En;
}

/**
 * Returns the current system language.
 * 
 * @staticvar Locale $lang
 * @param String $set Used to change the system language. Otherwise it will be
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
	if ($set instanceof \spitfire\Context) {
		$context = $set;
		return;
	}
	
	# If we have chosen one retrieve it 
	if ($set != null) {
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

function validate($target = null, Validator$validator = null) {
	if ($target !== null && $target instanceof Validatable) {
		$v = new Validator();
		return $v->test($target);
	}
	elseif ($validator !== null) {
		$result = $validator->test($target);
		if (!$result->success()) {	throw Validator::makeException($result); }
		return $result;
	}
	return new Validator();
}