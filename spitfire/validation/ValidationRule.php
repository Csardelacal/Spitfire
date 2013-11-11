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
	 * @param mixed $source This value allows to define the origin of an error, 
	 *         allowing to attach this data next to the source. For example, in forms
	 *         you can render the errors next to the field.
	 */
	function test($value, &$source = null);
}