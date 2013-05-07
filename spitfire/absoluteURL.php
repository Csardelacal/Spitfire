<?php

use spitfire\environment;
use spitfire\Request;

class absoluteURL extends URL
{
	
	public $subdomain;
	
	public function setSubdomain($subdomain) {
		$this->subdomain = $subdomain;
	}
	
	public function getSubDomain() {
		return $this->subdomain;
	}
	
	public static function canonical() {
		$r = Request::get();
		$canonical = new self($_GET);
		
		$path   = $r->getControllerURI();
		$path[] = $r->getAction();
		array_merge($path, $r->getObject());
		
		$canonical->setPath($path);
		$canonical->setExtension($r->getExtension());
		
		return $canonical;
	}

	public function __toString() {
		$rel = parent::__toString();
		$proto  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$server = environment::get('server_name')? environment::get('server_name') : $_SERVER['SERVER_NAME'];
		$subdomain = $this->subdomain? $this->subdomain . '.' : '';
		
		return $proto . $subdomain . $server . $rel;
	}
}
