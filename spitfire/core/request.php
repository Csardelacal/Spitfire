<?php

namespace spitfire;

use Controller;
use App;
use Headers;

/**
 * The request class is a component that allows developers to retrieve information
 * of how the current user's request is being handled, what app has been chosen
 * to handle it and which controller is in charge of handling it.
 */
class Request
{
	private $path;
	
	private $app;
	private $controller;
	private $action;
	private $object;
	private $answerformat;
	
	private $headers;
	
	static  $instance;
	
	protected function __construct($pathinfo = null) {
		if (is_null($pathinfo)) $pathinfo = $_SERVER['PATH_INFO'];
		$this->headers = new Headers();
		$this->path = $pathinfo;
		self::$instance = $this;
	}
	
	public function setApp(App$app) {
		$this->app = $app;
	}
	
	public function getApp() {
		if (!$this->app) $this->init();
		return $this->app;
	}
	
	public function getControllerURI() {
		return explode('\\', substr(get_class($this->getController()), strlen($this->app->getClassNameSpace()), 0-strlen('Controller')));
	}
	
	public function setController(Controller$controller) {
		$this->controller = $controller;
	}
	
	public function getController() {
		if (!$this->controller) $this->init();
		return $this->controller;
	}
	
	public function setAction($action) {
		if (!$action) $this->action = environment::get('default_action');
		else $this->action = $action;
	}
	
	public function getAction() {
		if (!$this->action) $this->init();
		return $this->action;
	}
	
	public function setObject($object) {
		$this->object = $object;
	}
	
	public function getObject() {
		if (!$this->object) $this->init();
		return $this->object;
	}
	
	public function setExtension($extension) {
		$this->answerformat = $extension;
	}

	public function getExtension() {
		if (!$this->answerformat) $this->init();
		$allowed = environment::get('supported_view_extensions');
		
		if ( in_array($this->answerformat, $allowed) )
			return $this->answerformat;
		else return $allowed[0];
	}
	
	/**
	 * Returns the headers object. This allows to manipulate the answer 
	 * headers for the current request.
	 * 
	 * @return Headers
	 */
	public function getHeaders() {
		return $this->headers;
	}
	
	public function handle() {
		$this->app->runTask($this->controller, $this->action, $this->object);
	}
	
	/**
	 * getPath()
	 * Reads the current path the user has selected and tries to detect
	 * Controllers, actions and objects from it.
	 * 
	 * [NOTICE] getPath does not guarantee safe input, you will have to
	 * manually check whether the input it received is valid.
	 * 
	 * @throws \publicException In case a controller with no callable action
	 *            has been found.
	 */
	public function init() {
		$path = array_filter(explode('/', $this->path));
		
		/* To fetch the extension requested by the user we do the
		 * following:
		 * * We get the last element of the path.
		 * * Split it by the .
		 * * Keep the first part as filename
		 * * And the rest as extension.
		 */
		$last      = explode('.', array_pop($path));
		$info      = (count($last) > 1)? array_pop($last) : '';
		$extension = (!empty($info))? $info : 'php';
		array_push($path, implode('.', $last));
			
		/* Try to get the current namespace, if one is registered
		 * we will redirect the request to another app.
		 */
		if (spitfire()->appExists(reset($path))) {
			$namespace = array_shift($path);
		}
		else $namespace = '';

		$app = spitfire()->getApp($namespace);
		$this->setApp($app);

		/* To get the controller and action of an element we 
		 * keep checking if each element is a valid controller,
		 * once it didn't find a valid controller it stops.
		 */
		if ($app->hasController(reset($path))){
			$controllerName = array_shift($path);
			$controller = $app->getController($controllerName);
		}
		else{
			$controller = $app->getController(environment::get('default_controller'));
			$controllerName = environment::get('default_controller');
		}

		if (is_callable(Array($controller, reset($path)))) {
			$action = array_shift($path);
		}
		elseif (!reset($path)) {
			$action = environment::get('default_action');
		}
		else {
			throw new \publicException('Action not Found', 404);
			$action = 'detail';
		}
		
		$this->setController($controller);
		$this->setAction($action);
		$this->setObject($path);
		$this->setExtension($extension);
	}
	
	public static function get($path = null) {
		if (self::$instance) return self::$instance;
		else return self::$instance = new self($path);
	}
}