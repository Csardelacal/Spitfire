<?php namespace spitfire\validation;

/**
 * Objects of this class represent the result of a validation. This means, they
 * store whether a test was successful or if a 
 */
class ValidationResult
{
	/**
	 * The list of errors that the testing of a validator generated. Testing this
	 * for empty indicates whether a test was successul or not.
	 * @var ValidationError[]
	 */
	private $errors;
	
	/**
	 * Creates a new validation result. This holds information about errors that 
	 * were found when validating the data that came into your system, usually
	 * for user data read from POST or GET.
	 * 
	 * @param ValidationError[] $errors
	 */
	public function __construct($errors = Array()) {
		$errs= Array();
		foreach ($errors as $error) {
			if ($error instanceof ValidationResult) $errs = array_merge ($errs, $error->getErrors());
			else {$errs[] = $error;}
		}
		$this->errors = array_filter($errs);
	}
	
	/**
	 * Determines whther the validation of the data was successful. In case it was 
	 * it will return a boolean true, otherwise a boolean false.
	 * 
	 * @return boolean Indicating whether the validation was successful or not
	 */
	public function success() {
		return empty($this->errors);
	}
	
	/**
	 * Returns the set of errors this validation generated. This allows testing where
	 * the errors generated and print them to the user.
	 */
	public function getErrors() {
		return $this->errors;
	}
	
	public function __toString() {
		if ($this->success()) { return ''; }
		
		return "<ul class=\"validationErrors\">" . implode($this->errors). "</ul>";
	}
	
}