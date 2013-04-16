<?php

namespace spitfire;

use \Controller;
use App;

class Request
{
	private $app;
	private $controller;
	private $controllerURI;

	private $action;
	private $object;
	private $answerformat = 'php';
	
	public function __construct() {
		$this->app = spitfire();
	}
	
	public function setApp(App$app) {
		$this->app = $app;
	}
	
	public function getApp() {
		return $this->app;
	}
	
	public function setControllerURI($controller) {
		$this->controllerURI = $controller;
	}
	
	public function getControllerURI() {
		return $this->controllerURI;
	}
	
	public function setController(Controller$controller) {
		$this->controller = $controller;
	}
	
	public function getController() {
		return $this->controller;
	}
	
	public function setAction($action) {
		$this->action = $action;
	}
	
	public function getAction() {
		return $this->action;
	}
	
	public function setObject($object) {
		$this->object = $object;
	}
	
	public function getObject() {
		return $this->object;
	}
	
	public function setExtension($extension) {
		$this->answerformat = $extension;
	}

	public function getExtension() {
		$allowed = environment::get('supported_view_extensions');
		
		if ( in_array($this->answerformat, $allowed) )
			return $this->answerformat;
		else return $allowed[0];
	}
	
	public function handle() {
		$this->app->runTask($this->controller, $this->action, $this->object);
	}
}