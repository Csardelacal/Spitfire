<?php namespace spitfire;

use Controller;
use publicException;
use privateException;

class Intent
{
	
	private $app;
	private $controller;
	private $action;
	private $object;
	
	/**
	 * Holds the view the app uses to handle the current request. This view is in 
	 * charge of renderring the page once the controller has finished processing
	 * it.
	 * 
	 * @var \spitfire\View
	 */
	private $view;
	
	function __construct(App$app = null, Controller$controller = null, $action = null, $object = null) {
		$this->app = $app;
		$this->controller = $controller;
		$this->action = $action;
		$this->object = $object;
	}

	public function setApp(App$app) {
		$this->app = $app;
	}
	
	public function getApp() {
		if (!$this->app) {
			$this->app = spitfire();
		}
		return $this->app;
	}
	
	public function setController(Controller$controller) {
		$this->controller = $controller;
	}
	
	public function getController() {
		return $this->controller;
	}
	
	public function setAction($action) {
		if (!$action) {
			$this->action = environment::get('default_action');
		} else {
			$this->action = $action;
		}
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
	
	public function getView() {
		if ($this->view) {
			return $this->view;
		} else {
			return $this->view = $this->app->getView($this->controller);
		}
	}
	
	public function run() {
		#Run the onload
		if (method_exists($this->controller, 'onload') ) {
			call_user_func_array(Array($this->controller, 'onload'), Array($this->action));
		}
		
		#Check if the controller can handle the request
		$request = Array($this->controller, $this->action);
		if (is_callable($request)) call_user_func_array($request, $this->object);
		else throw new publicException('Page not found', 404, new privateException('Action not found', 0));
	}
}