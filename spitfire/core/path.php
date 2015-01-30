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
	  * The namespace of the app being used. This allows spitfire to locate the 
	  * application for the namespace and correctly handle it.
	  * 
	  * @var string 
	  */
	 private $app;
	 
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
	  * @var array
	  */
	 private $parameters;
	 
	 /**
	  * The Path object contains the data that is obtained from the INFO_PATH part
	  * of the URL. It allows the Router to define the controller, action, object,
	  * the expected response format and the parameters it took from the URL.
	  * 
	  * 
	  * @param string $app
	  * @param array  $controller
	  * @param string $action
	  * @param array  $object
	  * @param string $format
	  * @param array  $parameters
	  */
	 public function __construct($app, $controller, $action, $object, $format = 'php', $parameters = Array()) {
		 $this->app        = $app;
		 $this->controller = (array)$controller;
		 $this->action     = $action;
		 $this->object     = (array)$object;
		 $this->format     = $format;
		 $this->parameters = $parameters;
	 }
	 
	 /**
	  * Returns the namespace of the app chosen to handle the request. This allows
	  * your application to quickly generate links pointing to the current app.
	  * 
	  * @return string
	  */
	 public function getApp() {
		 return $this->app;
	 }
	 
	 /**
	  * Changes the app in charge of handling the requested Path. Please note that
	  * this is required to be the namespace of the app, so you will have to retrieve
	  * this from the app in case you wanna use it.
	  * 
	  * @param string $app
	  * @return \spitfire\core\Path
	  */
	 public function setApp($app) {
		 #If the App is actually an App and the user didn't read the doc, we will
		 #forgive him.
		 if ($app instanceof \App) {
			 $app = $app->getNameSpace();
		 }
		 $this->app = $app;
		 return $this;
	 }
	 
	 /**
	  * Returns the controller name in array format. This allows you to manipulate
	  * the fragments easily.
	  * 
	  * @return string[]
	  */
	 public function getController() {
		 return $this->controller ? $this->controller : (array) \spitfire\environment::get('default_controller');
	 }
	 
	 /**
	  * Returns the action the Router parsed from the URL. The action could also
	  * be a sub-controller, so be aware that this may change during the execution 
	  * of the request. 
	  * 
	  * Usually that's not an issue for the user though.
	  * 
	  * @return string
	  */
	 public function getAction() {
		 return $this->action ? $this->action : \spitfire\environment::get('default_action');
	 }
	 
	 /**
	  * Get's the object (the unnamed parameters of the action) that is passed to
	  * your application via the URL.
	  * 
	  * @return string[]
	  */
	 public function getObject() {
		 return $this->object;
	 }
	 
	 /**
	  * Returns the extension appended to the URL and that indicates the type of 
	  * response the client expects. Your application is free to adequate to this
	  * or ignore the value.
	  * 
	  * @return string
	  */
	 public function getFormat() {
		 return $this->format ? $this->format : 'php';
	 }
	 
	 /**
	  * Returns the named parameters that came with the URL. THis are parsed by 
	  * the router in case it locates named parameter in a user defined route.
	  * This are especially useful when you want to report data of a url fragment
	  * that may alter your route (like the language).
	  * 
	  * @return string[]
	  */
	 public function getParameters() {
		 return $this->parameters;
	 }
	 
	 /**
	  * Sets the controller name of the class that should handle the request. The
	  * controller name of \some\randomController would be Array(some, random).
	  * 
	  * @param string[] $controller
	  * @return \spitfire\core\Path
	  */
	 public function setController($controller) {
		 $this->controller = $controller;
		 return $this;
	 }
	 
	 /**
	  * The name of the action that should be used to respond to the request.
	  * 
	  * @param string $action
	  * @return \spitfire\core\Path
	  */
	 public function setAction($action) {
		 $this->action = $action;
		 return $this;
	 }
	 
	 /**
	  * Defines the object of the request. The object is an array which holds the
	  * parameters that will be sent to the action method of the controller.
	  * 
	  * @param string[] $object
	  * @return \spitfire\core\Path
	  */
	 public function setObject($object) {
		 $this->object = $object;
		 return $this;
	 }
	 
	 /**
	  * Defines the format which the app should try to respond with. If your app
	  * is being called directly from a browser this should be something like 
	  * html, if it's an API you could use JSON, XML or RSS output.
	  * 
	  * @param string $format
	  * @return \spitfire\core\Path
	  */
	 public function setFormat($format) {
		 $this->format = $format;
		 return $this;
	 }
	 
	 public function setExtension($extension) {
		 return $this->setFormat($extension);
	 }


	 /**
	  * Defines the named parameters that the router (or you) found. These can be
	  * retrieved by the application by accessing the request object and let you
	  * define parameters in seemingly random positions of a route.
	  * 
	  * @param string $parameters
	  * @return \spitfire\core\Path
	  */
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