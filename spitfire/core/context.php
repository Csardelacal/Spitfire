<?php namespace spitfire;

use publicException;
use privateException;
use spitfire\InputSanitizer;

/**
 * The context is a wrapper for an application that 'does stuff' it basically
 * gathers a basic set of MVC tools and executes them to let things happen.
 * 
 * @link http://www.spitfirephp.com/wiki/index.php/Class:Context
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-10-25
 */
class Context
{
	public $context;
	
	public $app;
	public $controller;
	public $action;
	public $object;
	public $extension;
	
	/**
	 * Holds the view the app uses to handle the current request. This view is in 
	 * charge of rendering the page once the controller has finished processing
	 * it.
	 * 
	 * @var \spitfire\View
	 */
	public $view;
	
	public $parameters;
	public $get;
	public $post;
	public $cache;
	public $request;
	public $response;
	
	function __construct() {
		$this->context = $this;
	}
	
	public static function create() {
		$context = new Context;
		$context->get = new InputSanitizer($_GET);
		$context->post = new InputSanitizer($_POST);
		$context->cache = MemcachedAdapter::getInstance();
		$context->request = Request::get();
		$context->parameters = new InputSanitizer($context->request->getParameters());
		$context->response = new Response($context);
		return $context;
	}
	
	public function run() {
		#Run the onload
		if (method_exists($this->controller, 'onload') ) {
			call_user_func_array(Array($this->controller, 'onload'), Array($this->action));
		}
		
		#Check if the controller can handle the request
		$request = Array($this->controller, $this->action);
		if (is_callable($request)) $_return = call_user_func_array($request, $this->object);
		else throw new publicException('Page not found', 404, new privateException('Action not found', 0));
		
		if ($_return instanceof Context) return $_return;
		else return $this;
	}
}