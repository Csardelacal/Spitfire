<?php namespace spitfire\validation;

class ValidationError
{
	private $message;
	private $extendedMessage;
	private $src;
	
	private $subErrors;
	
	public function __construct($message, $extendedMessage = '', $src = null) {
		$this->message = $message;
		$this->extendedMessage = $extendedMessage;
		$this->src = $src;
	}
	
	public function getMessage() {
		return $this->message;
	}

	public function getExtendedMessage() {
		return $this->extendedMessage;
	}

	public function getSrc() {
		return $this->src;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	public function setExtendedMessage($extendedMessage) {
		$this->extendedMessage = $extendedMessage;
	}

	public function setSrc($src) {
		$this->src = $src;
	}
	
	public function getSubErrors() {
		return $this->subErrors;
	}

	public function putSubError($subError) {
		$this->subErrors[] = $subError;
		return $this;
	}

	public function setSubErrors($subErrors) {
		$this->subErrors = $subErrors;
		return $this;
	}
	
	public function __toString() {
		$_return = '<li>';
		$_return.= $this->message;
		$_return.= ($this->extendedMessage)? $this->extendedMessage : '';
		$_return.= ($this->subErrors)? '<ul>' . implode('', $this->subErrors) . '</ul>' : '';
		$_return.= '</li>';
	}
}