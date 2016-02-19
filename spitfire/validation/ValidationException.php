<?php namespace spitfire\validation;

/**
 * Validation exceptions are generated after a value for data has been tested 
 * and it has been deemed as faulty. If this happens all errors gathered will
 * be attached to this Exception and thrown to the application.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class ValidationException extends Exception
{
	/**
	 * The result of the validation.
	 * @var \spitfire\validation\ValidationError[]
	 */
	private $result;
	
	/**
	 * Creates a validation exception. This object will contain the errors that 
	 * happened during validation of any information youu tested.
	 * 
	 * @param mixed $message
	 * @param mixed $code
	 * @param \spitfire\validation\ValidationError $errors
	 */
	public function __construct($message, $code, $errors) {
		$this->result = $errors;
		parent::__construct($message, $code, null);
	}
	
	/**
	 * Returns the validation result that contains errors for the application to
	 * use and print the errors.
	 * 
	 * @return \spitfire\validation\ValidationError[]
	 */
	public function getResult() {
		return $this->result;
	}
	
}