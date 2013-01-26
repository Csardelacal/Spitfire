<?php

use spitfire\environment;
use spitfire\SpitFire;

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
	private $extension = 'php';
	
	public function __construct($c = false, $a= false, $o = false, $p = Array()) {
		if($c) $this->controller = (is_array($c)) ? $c : Array($c);
		else $this->controller = ( is_array(environment::get('default_controller')) )? environment::get('default_controller'):Array(environment::get('default_controller'));

		if($a) $this->action = $a;
		else $this->action = environment::get('default_action');

		if($o) $this->object = (is_array($o)) ? $o : Array($o);
		else $this->object = environment::get('default_object');

		$this->params = $p;
	}
	
	public function setController ($controller) {
		$this->controller = $controller;
	}
	
	public function getController() {
		return $this->controller;
	}
	
	public function setAction ($action) {
		$this->action = $action;
	}
	
	public function getAction() {
		return $this->action;
	}
	
	public function setObject ($object) {
		$this->object = $object;
	}
	
	public function getObject() {
		return $this->object;
	}
	
	public function setExtension($extension) {
		if (! empty($extension) )
		$this->extension = $extension;
	}
	
	public function getExtension() {
		return $this->extension;
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
		
		if ( is_array($this->object) ) $object = implode('/', $this->object);
		else $object = $this->object;
		
		if ( is_array($this->controller) ) $controller = implode('/', $this->controller);
		else $controller = $this->controller;
		
		$str =  SpitFire::baseUrl().
				'/'. $controller.
				'/'. $this->action.
				'/'. $object;
		
		if ($this->extension != 'php') $str.= ".$this->extension";
		
		$first = true;
		foreach ($this->params as $k => $v) {
			$str.= (($first)?'?':'&').urlencode($k).'='.urlencode($v);
			$first = false; 
		}
		
		return $str;
	}
	
	public static function asset($asset_name) {
		return SpitFire::baseUrl() . '/assets/' . $asset_name;
	}
}