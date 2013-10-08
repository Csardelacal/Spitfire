<?php

use spitfire\environment;
use spitfire\Request;

class absoluteURL extends URL
{
	
	public $domain;
	
	/**
	 * Set the domain name this URL points to. This is intended to address
	 * Spitfire apps that work on a multi-domain environment / subdomains
	 * and require linking to itself on another domain. They are also good 
	 * for sharing / email links where the URL without server name would
	 * be useless.
	 * 
	 * @param string $domain The domain of the URL. I.e. www.google.com
	 * @return absoluteURL
	 */
	public function setDomain($domain) {
		$this->domain = $domain;
		return $this;
	}
	
	public function getDomain() {
		return $this->domain;
	}
	
	public static function current() {
		return new self($_SERVER['PATH_INFO'], $_GET);
	}
	
	public static function canonical() {
		$r = Request::get();
		$canonical = new self($_GET);
		
		$default_controller = environment::get('default_controller');
		$default_action     = environment::get('default_action');
		
		$path   = $r->getIntent()->getApp()->getControllerURI($r->getIntent()->getController());
		if (count($path) == 1 && reset($path) == $default_controller) {
			$path = Array();
		}
		
		$action = $r->getIntent()->getAction();
		if ($action != $default_action) {
			$path[] = $action;
		}
		
		array_merge($path, $r->getIntent()->getObject());
		
		$canonical->setPath($path);
		$canonical->setExtension($r->getExtension());
		
		return $canonical;
	}

	public function __toString() {
		$rel = parent::__toString();
		$proto  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$server = environment::get('server_name')? environment::get('server_name') : $_SERVER['SERVER_NAME'];
		$domain = $this->domain? $this->domain : $server;
		
		return $proto . $domain . $rel;
	}
}
