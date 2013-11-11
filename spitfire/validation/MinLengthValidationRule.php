<?php namespace spitfire\validation;

class MinLengthValidationRule implements ValidationRule
{
	private $length;
	private $message;
	private $extendedMessage;
	
	public function __construct($length, $message, $extendedMessage = '') {
		$this->length = $length;
		$this->message = $message;
		$this->extendedMessage = $extendedMessage;
	}
	
	public function test($value, &$source = null) {
		if (strlen($value) < $this->length) {
			return new ValidationError($this->message, $this->extendedMessage, $source);
		}
		return false;
	}

}