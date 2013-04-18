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
class URL implements ArrayAccess
{
	//Private
	private $app;
	private $path = Array();
	private $params = Array();
	private $extension = 'php';
	
	public function __construct() {
		$params = func_get_args();
		
		foreach ($params as $param) {
			if (is_array($param)) $this->params = $param;
			elseif (is_a($param, 'App')) $this->app = $param;
			elseif (strstr($param, '/') || strstr($param, '?')) {
				$info = parse_url($param);
				$this->path = array_merge ($this->path, explode('/', $info['path']));
				if (isset($info['query'])) {
					$this->params = parse_str($info['query']);
				}
			}
			else $this->path[] = $param;
		}
	}
	
	public function setExtension($extension) {
		if (! empty($extension) )
		$this->extension = $extension;
	}
	
	public function getExtension() {
		return $this->extension;
	}
	
	public function setPath($path) {
		if (! empty($path) )
		$this->path = $path;
	}
	
	public function setApp($app) {
		if (! empty($app) )
		$this->app = $app;
	}
	
	public function getApp() {
		return $this->app;
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
		
		if ($this->app) $path = array_unshift ($path, $this->app->namespace);
		$path = implode('/', array_filter($this->path));
		
		$str =  SpitFire::baseUrl().'/'. $path;
		
		if ($this->extension != 'php') $str.= ".$this->extension";
		
		$first = true;
		foreach ($this->params as $k => $v) {
			$str.= (($first)?'?':'&').urlencode($k).'='.urlencode($v);
			$first = false; 
		}
		
		return $str;
	}
	
	public static function asset($asset_name, $app = null) {
		if ($app == null) return SpitFire::baseUrl() . '/assets/' . $asset_name;
		else return SpitFire::baseUrl() . '/' . $app->getAssetDirectory() . $asset_name;
	}
	
	public static function make($url) {
		return SpitFire::baseUrl() . $url;
	}
	
	public static function current() {
		return new self($_SERVER['PATH_INFO'], $_GET);
	}

	public function offsetExists($offset) {
		if (is_numeric($offset)) return isset($this->path[$offset]);
		else return isset($this->params[$offset]);
	}

	public function offsetGet($offset) {
		if (is_numeric($offset)) return $this->path[$offset];
		else return $this->params[$offset];
	}

	public function offsetSet($offset, $value) {
		if (is_numeric($offset)) return $this->path[$offset] = $value;
		else return $this->params[$offset] = $value;
	}

	public function offsetUnset($offset) {
		if (is_numeric($offset)) unset($this->path[$offset]);
		else unset( $this->params[$offset]);
	}
}