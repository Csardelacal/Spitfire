<?php

class Strings
{
	
	/**
	 * Turns camelCased strings into under_scored strings
	 * @param String $str
	 */
	public static function camel2underscores ($str) {
		$str = preg_replace('/[A-Z]/', '_$0', $str);
		if ($str[0] == '_') $str = substr ($str, 1);
		return strtolower($str);
	}
	
	public static function ellipsis ($str, $targetlength, $char = '...') {
		$newlen = $targetlength - strlen($char);
		return (strlen($str) > $newlen)? substr($str, 0, $newlen) . '...' : $str;
	}
	
	public static function slug($string) {
		$str = preg_replace(
				  /*http://stackoverflow.com/questions/10444885/php-replace-foreign-characters-in-a-string*/
				  ['/&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);/i','/[[:^print:]]/'], 
				  ['$1',''], 
				  htmlentities($string, ENT_QUOTES, 'UTF-8'));
		
		return strtolower(str_replace(Array(' ', '--'), Array('-', '-'), $str));
	}
	
	public static function endsWith($haystack, $needle) {
		return strcmp(substr($haystack, 0 - strlen($needle)), $needle) === 0;
	}
	
	public static function startsWith($haystack, $needle) {
		return strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0;
	}
	
	public static function plural($string) {
		if (Strings::endsWith($string, 'y')) return substr($string, 0, -1) .'ies';
		else return $string . 's';
	}
	
	public static function singular($string) {
		if (Strings::endsWith($string, 'ies'))   return substr($string, 0, -3) .'y';
		elseif (Strings::endsWith($string, 's')) return substr($string, 0, -1);
	}
	
}