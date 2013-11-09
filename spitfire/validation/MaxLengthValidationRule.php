<?php namespace spitfire\validation;

class MaxLengthValidationRule implements ValidationRule
{
	private $length;
	private $message;
	private $extendedMessage;
	
	public function __construct($length, $message, $extendedMessage = '') {
		$this->length = $length;
		$this->message = $message;
		$this->extendedMessage = $extendedMessage;
	}
	
	public function test($value, $source = null) {
		$errors = Array();
		if (strlen($value) > $this->length) {
			$errors[] = new ValidationError($this->message, $this->extendedMessage, $source);
		}
		
		return new ValidationResult($errors);
	}

}