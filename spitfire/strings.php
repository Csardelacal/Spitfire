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
		$str = preg_replace('/[[:^print:]]/', '', $string);
		return str_replace(Array('  ', ' '), Array(' ', '-'), $str);
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