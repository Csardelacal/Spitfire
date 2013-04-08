<?php

class Headers
{
	private $headers = Array(
	    'Content-type' => 'text/html;charset=utf-8',
	    'x-Powered-By' => 'Spitfire',
	    'x-version'    => '0.1 Beta'
	);
	
	public function set ($header, $value) {
		$this->headers[$header] = $value;
	}
	
	public function send () {
		foreach ($this->headers as $header => $value) {
			header("$header: $value");
		}
	}
	
	public function contentType($str) {
		switch ($str) {
			case 'php':
			case 'html':
				$this->set('Content-type', 'text/html;charset=utf-8');
			case 'xml':
				$this->set('Content-type', 'text/xml;charset=utf-8');
			case 'json':
				$this->set('Content-type', 'application/json;charset=utf-8');
		}
	}
}
