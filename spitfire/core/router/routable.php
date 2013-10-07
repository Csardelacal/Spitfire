<?php namespace spitfire\router;

abstract class Routable
{
	public function request($pattern, $target) {
		return $this->addRoute($pattern, $target, Route::METHOD_GET|Route::METHOD_POST);
	}
	
	public function get($pattern, $target) {
		return $this->addRoute($pattern, $target, Route::METHOD_GET);
	}
	
	public function put($pattern, $target) {
		return $this->addRoute($pattern, $target, Route::METHOD_PUT);
	}
	
	public function post($pattern, $target) {
		return $this->addRoute($pattern, $target, Route::METHOD_POST);
	}
	
	abstract public function addRoute($pattern, $target, $method = 0x03);
}