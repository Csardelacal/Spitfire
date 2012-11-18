<?php

define('E_PAGE_NOT_FOUND', 'Page not found', true);
define('E_PAGE_NOT_FOUND_CODE', 404, true);


/**
 * Get the Server's domains from highest to lowest
 * @author César
 * @package nLive.mvc
 * @return mixed Domains sorted from highest to lowest
 */
function getDomain($pos = false) {
	$domains = explode('.', $_SERVER['HTTP_HOST']);
	$domains = array_reverse($domains);
	if ((int)$domains[0]) return false;
	if (!isset($domains[2])) $domains[2] = ''; //If we're not using a subdomain
	
	if ($pos !==false) return $domains[$pos];
	else return $domains;
}

/**
 * 
 * This dinamically generates system urls
 * this allows us to validate URLs if needed
 * or generate different types of them depending
 * on if pretty links is enabled
 * @author C�sar
 *
 */
class URL
{
	//Private
	private $controller;
	private $action;
	private $object;
	private $params;
	
	public function __construct($c = false, $a= false, $o = false, $p = Array()) {
		if($c) $this->controller = $c;
		else $this->controller = environment::get('default_controller');

		if($a) $this->action = $a;
		else $this->action = environment::get('default_action');

		if($o) {
			if (is_array($o)) $this->object = implode('/', $object);
			else              $this->object = $o;
		}
		else $this->object = implode('/', environment::get('default_object'));

		$this->params = $p;
	}
	
	/**
	 * Sets a parameter for the URL's GET
	 * @param string $param
	 * @param string $value
	 * 
	 * [NOTICE] This function accepts parameters like controller,
	 * action or object that are part of the specification of nlive's 
	 * core. It is highly recommended not to use this "reserved words" 
	 * as parameters as they may cause the real values of these to be
	 * overwritten when the browser requests the site linked by these.
	 */
	public function setParam($param, $value) {
		$this->params[$param] = $value;
	}
	
	/**
	 * __toString()
	 * This function generates a URL for any page that nLive handles,
	 * it's output depends on if pretty / rewritten urls are active.
	 * If they are it will return /controller/action/object?params
	 * based urls and in any other case it'll be a normal GET based
	 * url.
	 */
	public function __toString() {
		if (environment::get('pretty_urls')) {
			$str =  SpitFire::baseUrl().
					'/'. $this->controller.
					'/'. $this->action.
					'/'. $this->object;
			$first = true;
			foreach ($this->params as $k => $v) {
				$str.= (($first)?'?':'&').urlencode($k).'='.urlencode($v);
				$first = false; 
			}
		} else {//pretty_urls
			$str = SpitFire::baseUrl().
					'/?controller='.$this->controller.
					'&action='.$this->action.
					'&object='.$this->object;
			foreach ($this->params as $k => $v) $str.= '&'.$k.'='.$v;
		}
		//$action = plugins::PRESET_URLTOSTRING;
		//BUG: $str = plugins::$action($str);
		return $str;
	}
	
	public static function asset($asset_name) {
		return SpitFire::baseUrl() . '/assets/' . $asset_name;
	}
}