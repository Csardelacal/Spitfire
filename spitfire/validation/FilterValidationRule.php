<?php namespace spitfire\validation;

/**
 * A filter based validation rule allows you to use premade PHP filters to validate
 * your content. Please note that a filter that sanitizes may cause unwanted
 * behavior or unexpected ones.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-11-09
 */
class FilterValidationRule implements ValidationRule
{
	private $filter;
	private $message;
	private $extendedMessage;
	
	public function __construct($filter, $message, $extendedMessage = '') {
		$this->filter = $filter;
		$this->message = $message;
		$this->extendedMessage = $extendedMessage;
	}
	
	public function test($value, $source = null) {
		$errors = Array();
		if (!filter_var($value, $this->filter)) {
			$errors[] = new ValidationError($this->message, $this->extendedMessage, $source);
		}
		
		return new ValidationResult($errors);
	}

}