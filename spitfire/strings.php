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
	
}