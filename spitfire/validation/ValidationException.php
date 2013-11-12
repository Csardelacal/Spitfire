<?php

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
	 * @var spitfire\validation\ValidationResult
	 */
	private $result;
	
	/**
	 * Returns the validation result that contains errors for the application to
	 * use and print the errors.
	 * 
	 * @return spitfire\validation\ValidationResult
	 */
	public function getResult() {
		return $this->result;
	}
	
	/**
	 * Sets the result that contains information about the errors this Exception
	 * carries.
	 * 
	 * @param spitfire\validation\ValidationResult $result
	 * @return \ValidationException
	 */
	public function setResult(spitfire\validation\ValidationResult$result) {
		$this->result = $result;
		return $this;
	}
	
}