<?php

abstract class Controller extends _SF_MVC
{
	
	//abstract public function index ($object, $params);
	//abstract public function detail ($object, $params);
	
	public function __construct() {
		$this->memcached = new _SF_Memcached();
		$this->call      = new _SF_Invoke();
		$this->post      = new _SF_InputSanitizer($_POST);
		$this->get       = new _SF_InputSanitizer($_GET);
	}
	
	public function __call($method, $args) {
		return $this->detail($method, $args);
		//else throw new BadMethodCallException('Invalid argument count. Requires two args.', 0);
	}
	
}