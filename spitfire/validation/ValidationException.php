<?php

class ValidationException extends Exception
{
	private $result;
	
	public function getResult() {
		return $this->result;
	}

	public function setResult(ValidationResult$result) {
		$this->result = $result;
		return $this;
	}
	
}