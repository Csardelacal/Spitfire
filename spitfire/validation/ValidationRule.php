<?php namespace spitfire\validation;

/**
 * Classes implementing this one indicate their ability to test a variable and 
 * verify that it's contents are according to what was expected.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
interface ValidationRule
{
	/**
	 * This method checks if the content of a variable is ok. If this is not so
	 * it will return a validation error.
	 * 
	 * @param mixed $value The value to be tested
	 * @return ValidationError|string|bool|null Return a boolean or null value if everything was alright.
	 */
	function test($value);
}