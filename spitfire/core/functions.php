<?php

use spitfire\SpitFire;
use spitfire\environment;
use spitfire\storage\database\DB;

/**
 * Shorthand function to create / retrieve the model the application is using
 * to store data.
 * 
 * @return DB
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