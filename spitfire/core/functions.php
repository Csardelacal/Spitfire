<?php

use spitfire\SpitFire;
use spitfire\environment;
use spitfire\Request;
use spitfire\locales\langInfo;
use spitfire\storage\database\DB;

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
	
	if (is_null($options) && !empty(SpitFire::$model)) return SpitFire::$model;
	
	#If the driver is not selected we get the one we want from env.
	if (!isset($options['db_driver'])) $driver = environment::get('db_driver');
	else $driver = $options['db_driver'];
	
	#Instantiate the driver
	$driver = 'spitfire\storage\database\drivers\\' . $driver . 'Driver';
	$driver = new $driver($options);
	#Store to main model
	if (is_null($options)) SpitFire::$model = $driver;
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
	
	$str = htmlentities($str, ENT_HTML5, environment::get('system_encoding'));
	if ($maxlength) return Strings::ellipsis ($str, $maxlength);
	else return $str;
}

function _c($amt) {
	$lang = lang();
	return __($lang->convertCurrency($amt)) . $lang->getCurrency();
}

/**
 * Returns the current system language.
 * 
 * @staticvar Locale $lang
 * @param String $set Used to change the system language. Otherwise it will be
 *               default-ed to Accept-Language header
 * @return Locale
 */
function lang($set = null) {
	static $lang = null;
	
	# If we have chosen one retrieve it 
	if ($set != null) {
		$lang = Request::get()->getApp()->getLocale($set);
	}
	
	#Else try to set one
	if ($lang == null) {
		$langs = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		
		foreach($langs as $l) {
			$l = new langInfo($l);
			if ($l->isUnderstood())	break;
		}
		
		$lang = $l->getLocaleClass();
		
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