<?php

define('E_PAGE_NOT_FOUND', 'Page not found', true);
define('E_PAGE_NOT_FOUND_CODE', 404, true);

/**
 * getPath()
 * Reads the current path the user has selected and tries to detect
 * Controllers, actions and objects from it.
 * 
 * [NOTICE] getPath does not guarantee safe input, you will have to
 * manually check whether the input it received is valid.
 * 
 * [NOTICE] getPath will prevent users from accessing any controllers
 * when in maintenance mode. But will also throw an error when the
 * user tries to enter maintenance mode when in normal operation.
 * This means you need to use a separate controller for generating
 * WiP sites, or manually edit it everytime you enter service mode.
 */
function getPath() {
	if (maintenance_enabled) {
		define ('controller', maintenance_controller, true);
		define ('action', false, true);
		define ('object', false, true);
		return true;
	} else {
		if (pretty_urls){
			$path_info = substr($_SERVER['PATH_INFO'], 1);
			$path = explode('/', $path_info);
			@list($controller, $action, $object) = $path;
			
			//Assign the object as array (with multiple elements) to a Global
			array_shift($path); array_shift($path);
			$GLOBALS['object_array'] = $path;
			
			//If the controller, action or object was left empty fill it with defaults 
			if (empty ($controller)) $controller = default_controller;
			if (empty ($action)) $action = default_action;
			if (empty ($object)) $object = default_object;
			
			//Check if invalid url's are being requested
			if ($controller == maintenance_controller) throw new publicException('User tried to access maintenance mode', 401);
			if (substr($action, 0,1) == '_') throw new publicException(E_PAGE_NOT_FOUND, E_PAGE_NOT_FOUND_CODE);
			
			define ('controller', $controller, false);
			define ('action', $action, true);
			define ('object', $object, true);
			return true;
		} else {//pretty_urls
			$controller = $_GET['controller'];
			$action = $_GET['action'];
			$object = $_GET['object'];
			
			if (!$controller) $controller = default_controller;
			if (!$action) $action = default_action;
			if (!$object) $object = default_object;
			
			define ('controller', $controller, true);
			define ('action', $action, true);
			define ('object', $$object, true);
			return true;
		}
	}
}

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
 * @author César
 *
 */
class URL
{
	//Private
	private $controller;
	private $action;
	private $object;
	private $params;
	
	public function __construct($c = default_controller, $a= default_action, $o = default_object, $p = Array()) {
		$this->controller = $c;
		$this->action = $a;
		$this->object = $o;
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
		if (pretty_urls) {
			$str =  base_url.
					'/'. $this->controller.
					'/'. $this->action.
					'/'. $this->object;
			$first = true;
			foreach ($this->params as $k => $v) {
				$str.= (($first)?'?':'&').urlencode($k).'='.urlencode($v);
				$first = false; 
			}
		} else {//pretty_urls
			$str =  base_url.
					'/?controller='.$this->controller.
					'&action='.$this->action.
					'&object='.$this->object;
			foreach ($this->params as $k => $v) $str.= '&'.$k.'='.$v;
		}
		//$action = plugins::PRESET_URLTOSTRING;
		//BUG: $str = plugins::$action($str);
		return $str;
	}
}