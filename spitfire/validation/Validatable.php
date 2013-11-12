<?php namespace spitfire\validation;

/**
 * Objects implementing this interface must have a validate method that must return
 * either a ValidationResult object or ValidationError containing the results of 
 * the validation executed.
 * 
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