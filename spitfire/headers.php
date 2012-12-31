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
}
