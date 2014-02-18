<?php namespace spitfire\core;

/**
 * The Path represents the set of data that Spitfire can obtain by parsing the 
 * path_info variable generated when using the framework. This object will provide
 * the data that you could obtain from an URL.
 * 
 * Paths are usually instantiated by the Router, which is the main use of the 
 * Path object. You can also instance it when mocking requests in Unit tests 
 * though.
 */
 class Path
 {
	 
	 /**
	  * The 'name' of the controller expected. The name of the controller is a list
	  * of string in an array. This is especially useful when mapping a URL
	  * (<code>/a/url</code>) to a Controller class (<code>\a\urlController</code>)
	  * 
	  * @var string[]
	  */
	 private $controller;
	 
	 /**
	  * The action of a Path is the name of the Method on the controller to be 
	  * invoked. This is usually mixed into the URL with the controller and may
	  * be indicating a 'subcontroller' during a certain amount of time.
	  * 
	  * @var string 
	  */
	 private $action;
	 
	 /**
	  * The list of strings that are passed as parameter after the action. The 
	  * app receives this parameters via the arguments of the action method.
	  * 
	  * @var string[] 
	  */
	 private $object;
	 
	 /**
	  * The response format the client expects to receive back from your application.
	  * This defaults to html (which is the most common response format for web
	  * apps) but can also be JSON / XML or any other extension you want.
	  * 
	  * @var string
	  */
	 private $format;
	 
	 /**
	  * The list of parameters the router parses from the route. This are the named
	  * params you are allowed to define and that can be across the URL, the server
	  * and even stuff excluded from the object.
	  * 
	  * @var string
	  */
	 private $parameters;
	 
	 /**
	  * The Path object contains the data that is obtained from the INFO_PATH part
	  * of the URL. It allows the Router to define the controller, action, object,
	  * the expected response format and the parameters it took from the URL.
	  * 
	  * 
	  * @param array  $controller
	  * @param string $action
	  * @param array  $object
	  * @param string $format
	  * @param array  $parameters
	  */
	 public function __construct($controller, $action, $object, $format = 'html', $parameters = Array()) {
		 $this->controller = (array)$controller;
		 $this->action     = $action;
		 $this->object     = (array)$object;
		 $this->format     = $format;
		 $this->parameters = $parameters;
	 }
	 
	 public function getController() {
		 return $this->controller;
	 }

	 public function getAction() {
		 return $this->action;
	 }

	 public function getObject() {
		 return $this->object;
	 }

	 public function getFormat() {
		 return $this->format;
	 }

	 public function getParameters() {
		 return $this->parameters;
	 }

	 public function setController($controller) {
		 $this->controller = $controller;
		 return $this;
	 }

	 public function setAction($action) {
		 $this->action = $action;
		 return $this;
	 }

	 public function setObject($object) {
		 $this->object = $object;
		 return $this;
	 }

	 public function setFormat($format) {
		 $this->format = $format;
		 return $this;
	 }

	 public function setParameters($parameters) {
		 $this->parameters = $parameters;
		 return $this;
	 }
	 
	 /**
	  * This is a small convenience method that allows the controller to delegate
	  * the task to a subcontroller as it hasn't been able to find any method able
	  * to handle the request with this Path.
	  * 
	  */
	 public function shift() {
		 $this->controller[] = $this->action;
		 $this->action = array_shift($this->object);
	 }
	 
 }