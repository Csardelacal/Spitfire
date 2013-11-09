<?php namespace spitfire\validation;

class ValidationResult
{
	
	private $errors;
	
	public function __construct($errors = Array()) {
		$this->errors = array_filter($errors);
	}
	
	public function success() {
		return empty($this->errors);
	}
	
	public function __toString() {
		if ($this->success()) { return ''; }
		
		return "<ul class=\"validationErrors\">" . implode($this->errors). "</ul>";
	}
	
}