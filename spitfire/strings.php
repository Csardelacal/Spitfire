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
				  '/&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);/i', 
				  '$1', //Remove accents
				  htmlentities($string, ENT_QUOTES, 'UTF-8'));
		
		return strtolower(preg_replace(
				  Array('/[^A-Za-z0-9_-\s]/', '/[ -_]+/'), 
				  Array('' /*Remove non-alphanumeric characters*/, '-' /*Remove multiple spaces*/), 
				  html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
	}
	
	public static function endsWith($haystack, $needle) {
		if (!$needle) return true;
		return strcmp(substr($haystack, 0 - strlen($needle)), $needle) === 0;
	}
	
	public static function startsWith($haystack, $needle) {
		return strpos($haystack, $needle) === 0;
	}
	
	public static function plural($string) {
		if (Strings::endsWith($string, 'y')) return substr($string, 0, -1) .'ies';
		else return $string . 's';
	}
	
	public static function singular($string) {
		if (Strings::endsWith($string, 'ies'))   return substr($string, 0, -3) .'y';
		elseif (Strings::endsWith($string, 's')) return substr($string, 0, -1);
		else                                     return $string;
	}
	
	public static function splitExtension($filename, $default = '') {
		/* To fetch the extension we do the following:
		 * * We get the last element of the path.
		 * * Split it by the .
		 * * Keep the first part as filename
		 * * And the rest as extension.
		 */
		$data = explode('.', $filename);
		
		if (count($data) > 1) {
			$extension = array_pop($data);
		} else {
			$extension = $default;
		}
		
		return Array(implode('.', $data), $extension);
	}
	
	public static function strToHTML($str) {
		
		$str = nl2br($str);
		
		return preg_replace_callback('/(http|https):\/\/([a-zA-z0-9\%\&\?\/\.]+)/', function($e) {
			$url = $e[0];
			$pretty = (strlen($e[2]) > 27)? substr($e[2], 0, 15) . '...' . substr($e[2], -10): $e[2];
			return sprintf('<a href="%s">%s</a>', $url, $pretty);
		},$str);
	}
	
}