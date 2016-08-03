<?php

class Strings
{
	
	/**
	 * Turns camelCased strings into under_scored strings. This is specially useful
	 * for class to URL conversion and the other way around.
	 * 
	 * @param String $str
	 * @return string
	 */
	public static function camel2underscores($str) {
		$_ret = preg_replace('/[A-Z]/', '_$0', $str);
		return strtolower(trim($_ret, '_'));
	}
	
	/**
	 * Converts under_score separated strings into camelCased. Allowing an application
	 * to retrieve a class name from a case insensitive environment.
	 * 
	 * @param string  $str  The input string (example: camel_case)
	 * @param boolean $high Defines whether the first letter should be uppercase. 
	 *                      "CamelCase" (true) or "camelCase" (false)
	 * @return string
	 */
	public static function underscores2camel($str, $high = true) {
		$_ret = preg_replace_callback('/\_[a-z]/', function ($e) { return strtoupper($e[0][1]); }, $str);
		return $high? ucfirst($_ret) : $_ret;
	}
	
	public static function ellipsis ($str, $targetlength, $char = '…') {
		$newlen = $targetlength - strlen($char);
		return (strlen($str) > $newlen)? substr($str, 0, $newlen) . '…' : $str;
	}
	
	public static function slug($string) {
		$str = preg_replace(
				  /*http://stackoverflow.com/questions/10444885/php-replace-foreign-characters-in-a-string*/
				  '/&([A-Za-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);/i', 
				  '$1', //Remove accents
				  htmlentities($string, ENT_QUOTES, 'UTF-8'));
		
		return strtolower(preg_replace(
				  Array('/[^A-Za-z0-9_\-\s]/', '/[ \-\_]+/'), 
				  Array('' /*Remove non-alphanumeric characters*/, '-' /*Remove multiple spaces*/), 
				  html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
	}
	
	public static function endsWith($haystack, $needle) {
		if (!$needle) { return true; }
		return strcmp(substr($haystack, 0 - strlen($needle)), $needle) === 0;
	}
	
	public static function startsWith($haystack, $needle) {
		if (empty($needle)) { return true; }
		return strpos($haystack, $needle) === 0;
	}
	
	public static function plural($string) {
		if (Strings::endsWith($string, 'y')) return substr($string, 0, -1) .'ies';
		else { return $string . 's'; }
	}
	
	public static function singular($string) {
		if (Strings::endsWith($string, 'ies'))   return substr($string, 0, -3) .'y';
		elseif (Strings::endsWith($string, 's')) return substr($string, 0, -1);
		else                                     return $string;
	}
	
	public static function strToHTML($str) {
		
		$str = nl2br($str);
		
		return preg_replace_callback('/(http|https):\/\/([a-zA-z0-9\%\&\?\/\.\-_\=]+)/', function($e) {
			$url = $e[0];
			$pretty = (strlen($e[2]) > 27)? substr($e[2], 0, 15) . '...' . substr($e[2], -10): $e[2];
			return sprintf('<a href="%s">%s</a>', $url, $pretty);
		},$str);
	}
	
	/**
	 * Offsets the line by a character. For example, when you're printing text to 
	 * HTML you wish it to be indented the same way as the rest of your HTML
	 * 
	 * @param string $str
	 * @param int    $times
	 * @param string $character
	 * @return string
	 */
	public static function indent($str, $times = 1, $character = "\t") {
		$offset = str_repeat($character, $times);
		return $offset . str_replace(PHP_EOL, PHP_EOL . $offset, $str);
	}
	
}
