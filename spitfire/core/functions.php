<?php

use spitfire\SpitFire;
use spitfire\storage\database\DB;

/**
 * Shorthand function to create / retrieve the model the application is using
 * to store data.
 * 
 * @return DB
 */
function model() {
	if (!empty(SpitFire::$model)) return SpitFire::$model;
	else return new DB();
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