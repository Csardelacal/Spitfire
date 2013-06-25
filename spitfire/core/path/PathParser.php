<?php

namespace spitfire\path;

use spitfire\Request;

/**
 * Any class that implements this one is intended to aid parsing the path a user
 * has requested. For example, if the user requests /this/is/a/path this parsers
 * will receive one (or various) of the elements.
 */
interface PathParser
{
	/**
	 * This allows the request to inform the parser about it's existence. It can
	 * always be replaced by Request::get(), but it's better to receive it here.
	 * 
	 * @param \spitfire\Request $request
	 */
	public function setRequest(Request$request);
	
	/**
	 * This is the main function of this elements. Here they will receive a token
	 * of the path and handle it accordingly. Depending on whether the parser could 
	 * handle the token or not it will return true or false.
	 * 
	 * If it returns true, Spitfire will continue sending tokens, if not it will
	 * stop and move to the next parser.
	 * 
	 * @param string $element
	 * @return boolean
	 */
	public function parseElement($element);
	
}