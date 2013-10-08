<?php namespace spitfire;

use Headers;

class Response
{
	/**
	 *
	 * @var Headers
	 */
	private $headers;
	private $body;
	private $sent = false;
	
	public function __construct($body, $status = 200, $headers = null) {
		$this->body    = $body;
		$this->headers = new Headers();
		$this->headers->status($status);
		
		if ($headers) {
			foreach ($headers as $header => $content) {
				$this->headers->set($header, $content);
			}
		}
	}
	
	/**
	 * Returns the headers object. This allows to manipulate the answer 
	 * headers for the current request.
	 * 
	 * @return Headers
	 */
	public function getHeaders() {
		return $this->headers;
	}

	public function getBody() {
		if (empty($this->body)) return Request::get()->getIntent()->getView()->render();
		return $this->body;
	}

	public function setHeaders(Headers $headers) {
		$this->headers = $headers;
		return $this;
	}

	public function setBody($body) {
		$this->body = $body;
		return $this;
	}
	
	public function wasSent() {
		return $this->sent;
	}
	
	public function send() {
		ob_start();
		$this->getBody();
		$this->headers->send();
		ob_flush();
	}
	
}