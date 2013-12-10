<?php

namespace spitfire;

use Strings;

/**
 * The request class is a component that allows developers to retrieve information
 * of how the current user's request is being handled, what app has been chosen
 * to handle it and which controller is in charge of handling it.
 */
class Request
{
	private $path;
	private $answerformat;
	
	private $response;
	
	static  $instance;
	private $parsers;
	private $parameters;
	
	protected function __construct() {
		$pinfo = get_path_info();
		
		$this->path     = $pinfo;
		$this->response = new Response(null);
		self::$instance = $this;
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
	
	public function getResponse() {
		return $this->response;
	}
	
	
	public function setParameters($parameters) {
		$this->parameters = $parameters;
	}
	
	public function getParameters() {
		return $this->parameters;
	}
	
	public function getParameter($name) {
		if (isset($this->parameters[$name])) {
			return $this->parameters[$name];
		}
	}
	
	public function makeContext($path) {
		$context = $this->readPath(Context::create());
		$context->view = $context->app->getView($context->controller);
		return $context;
	}


	/**
	 * Reads the current path the user has selected and tries to detect
	 * Controllers, actions and objects from it.
	 * 
	 * [NOTICE] readPath does not guarantee safe input, you will have to
	 * manually check whether the input it received is valid.
	 * 
	 * @throws \publicException In case a controller with no callable action
	 *            has been found.
	 */
	public function readPath($context) {
		$path = array_filter(explode('/', $this->path));
		
		list($last, $extension) = Strings::splitExtension(array_pop($path), 'php');
		array_push($path, $last);
		
		$handlers = $this->getHandlers();
		foreach ($handlers as $handler) {
			if ($handler($context, reset($path))) {
				array_shift($path);
			}
		}
		
		$context->object = $path;
		$this->setExtension($context->extension = $extension);
		return $context;
	}
	
	public function addHandler($parser) {
		$this->parsers[] = $parser;
	}
	
	public function getHandlers() {
		return $this->parsers;
	}

	/**
	 * This function allows to use a singleton pattern on the request. We don't 
	 * need more than one request at a time, so we can use this function to avoid
	 * storing it in several places.
	 * 
	 * @param string $path
	 * @return \Request
	 */
	public static function get($path = null) {
		if (self::$instance) return self::$instance;
		else return self::$instance = new self($path);
	}
}