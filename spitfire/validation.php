<?php

/**
 * This file contains all the functions that allow user data
 * validation.
 * @author César de la Cal <cesar@magic3w.com>
 * @package nLive.validation
 */

/**
 * Checks if the entered string is a valid username.
 * @param string $username
 * @package nLive.validation
 */
function valid_username($username) {
	/**
	 * This contains valid characters that should not be matched as invalid
	 * by the function but are actually matched by ctype_alnum and would
	 * cause "false-positive" invalid usernames
	 * @var array $valid_chars
	 */
	$valid_chars = Array('_', '-');
	if (!$username[3]) return false;
	foreach ($valid_chars as $char) $username = str_replace($char, '', $username);
	//Check whether the string we entered starts with a letter and the rest of the
	//string is a combination of numbers / letters.
	if (!is_numeric(substr($username, 0, 1)) && ctype_alnum($username)) return true;
	else return false;
}

/**
 * Parses Text and detects URLs and rewrites them into html tags
 * @param string $str contains the text to be parsed
 * @return string parsed string with replaced html links
 */
function parse_urls ($str) {
	
	/**
	 * Callback function used to handle matches.
	 * @param $matches data matched
	 */
	$parse_links_callback = '_SF_PARSE_LINKS_CALLBACK';
	
	function _SF_PARSE_LINKS_CALLBACK ($matches) {
		if ( $url = parse_url($matches[0]) ) {
			if ($url['host'] == $_SERVER['HTTP_HOST']) {
				if ( isset($url['path']) ) {
					$path = substr($url['path'], 1);
					$path = explode('/', $path);
					
					$controller = (isset($path[0])) ? 'controller_' . $path[0] : '';
					$action     = '_resolve_' . $path[1];
					$object     = (isset($path[2])) ? $path[2] : '';
					
					$method     = Array($controller, $action);
					$args       = Array($object);
					
					try {
						class_exists($controller, '_autoload'); 
						$class_exists = true;
					} catch (Exception $e) {
						$class_exists = false;
					}
					
					if ($class_exists && is_callable($method)) $anchor_text = call_user_func_array($method, $args);
					else $anchor_text = $matches[0];
					
					return '<a href="'.$matches[0].'">'. $anchor_text . '</a>';
					//TODO: Try to read a anchor text from Controller
				}
			} else {
				return '<a href="'.$matches[0].'">'. $matches[0] . '</a>';
			}
		}
	}; //parse_links_callback
	
	return preg_replace_callback('/(http|https):\/\/([^\s]+)/', $parse_links_callback, $str);
}//parse_urls