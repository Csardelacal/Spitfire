<?php

use spitfire\environment;

class Headers
{
	private $headers = Array(
	    'Content-type' => 'text/html;charset=utf-8',
	    'x-Powered-By' => 'Spitfire',
	    'x-version'    => '0.1 Beta'
	);
	
	private $states = Array(
		 200 => '200 OK',
		 301 => '301 Moved Permanently',
		 302 => '302 Found',
		 401 => '401 Unauthorized',
		 403 => '403 Forbidden',
		 404 => '404 Not Found',
		 500 => '500 Server Error'
	);
	
	public function set ($header, $value) {
		$this->headers[$header] = $value;
	}
	
	public function send () {
		foreach ($this->headers as $header => $value) {
			
			#Special condition for status headers
			if ($header == 'Status') {
				header("HTTP/1.1 $value");
				continue;
			}
			
			header("$header: $value");
		}
	}
	
	public function contentType($str) {
		
		$encoding = environment::get('system_encoding');
		
		switch ($str) {
			case 'php':
			case 'html':
				$this->set('Content-type', 'text/html;charset=' . $encoding);
				break;
			case 'xml':
				$this->set('Content-type', 'text/xml;charset=' . $encoding);
				break;
			case 'json':
				$this->set('Content-type', 'application/json;charset=' . $encoding);
				break;
			default:
				$this->set('Content-type', $str);
		}
	}
	
	public function status($code= 200) {
		$this->set('Status', $this->states[$code]);
	}
	
	public function redirect($location, $status = 302) {
		$this->set('Location', $location);
		$this->set('Expires', date("r", time()));
		$this->set('Cache-Control', 'no-cache, must-revalidate');
		$this->set('Status', $this->states[$status]);
	}
	
}
