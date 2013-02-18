<?php

use spitfire\SpitFire;
use spitfire\environment;
use spitfire\storage\database\DB;

function spitfire() {
	static $sf;
	if ($sf) return $sf;
	
	$sf = new SpitFire();
	$sf->fire();
	return $sf;
}

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
 * @return DB|\spitfire\storage\database\DB
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

function lang($set = null) {
	static $lang = null;
	if ($set != null) $lang = $set;
	if ($lang == null) $lang = 'en';
	return $lang;
}

function _t() {
	static $lang = null;
	if ($lang == null) {
		$classname = lang() . 'Locale';
		$lang = new $classname();
	}
	return call_user_func_array(Array($lang, 'say'), func_get_args());
}