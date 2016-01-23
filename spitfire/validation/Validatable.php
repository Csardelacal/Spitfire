<?php namespace spitfire\validation;

/**
 * Objects implementing this interface must have a validate method that must return
 * either a ValidationResult object or ValidationError containing the results of 
 * the validation executed.
 * 
 * @deprecated since version 0.1-dev 2016.01.22
 * @author César de la Cal<cesar@magic3w.com>
 */
interface Validatable 
{
	/**
	 * Allows the object to test it's contents and see if the data it holds is correct
	 * and safe for usage or writing into a database.
	 * 
	 * @return ValidationResult|ValidationError
	 */
	function validate();
}