<?php

use spitfire\SpitFire;
use spitfire\environment;
use spitfire\core\Request;

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
		return new self(get_path_info(), $_GET);
	}
	
	public static function asset($asset_name, $app = null) {
		if ($app == null) { $path = SpitFire::baseUrl() . '/assets/' . $asset_name; }
		else { $path = SpitFire::baseUrl() . '/' . $app->getAssetsDirectory() . $asset_name; }
		
		$proto  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$server = environment::get('server_name')? environment::get('server_name') : $_SERVER['SERVER_NAME'];
		
		return $proto . $server . $path;
	}
	
	public static function canonical() {
		
		#Get the relative canonical URI
		$canonical = URL::canonical();
		
		$proto  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$server = environment::get('server_name')? environment::get('server_name') : $_SERVER['SERVER_NAME'];
		
		#Prepend protocol and server and return it
		return $proto . $server . $canonical;
	}

	public function __toString() {
		$rel = parent::__toString();
		$proto  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$server = environment::get('server_name')? environment::get('server_name') : $_SERVER['SERVER_NAME'];
		$domain = $this->domain? $this->domain : $server;
		
		return $proto . $domain . $rel;
	}
}
