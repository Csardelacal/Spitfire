<?php

namespace spitfire;

use \Controller;
use App;
use URL;

/**
 * The request class is a component that allows developers to retrieve information
 * of how the current user's request is being handled, what app has been chosen
 * to handle it and which controller is in charge of handling it.
 */
class Request
{
	private $app;
	private $controller;
	private $controllerURI;

	private $action;
	private $object;
	private $answerformat = 'php';
	
	private $url;
	static  $instance;
	
	public function __construct() {
		$this->app = spitfire();
		$this->url = new URL($_SERVER['PATH_INFO'], $_GET);
		
		self::$instance = $this;
	}
	
	public function setApp(App$app) {
		$this->app = $app;
	}
	
	public function getApp() {
		return $this->app;
	}
	
	public function setControllerURI($controller) {
		if (is_string($controller)) {
			$this->controllerURI = explode ('\\', $controller);
		}
		else {
			$this->controllerURI = $controller;
		}
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
		if (!$action) $this->action = environment::get('default_action');
		else $this->action = $action;
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
	
	public function getCurrentURL() {
		return $this->url;
	}
	
	public function handle() {
		$this->app->runTask($this->controller, $this->action, $this->object);
	}
	
	public static function get() {
		return self::$instance;
	}
}