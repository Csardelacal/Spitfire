<?php namespace spitfire\core;

use spitfire\core\Request;
use spitfire\cache\MemcachedAdapter;
use publicException;
use spitfire\exceptions\PrivateException;
use spitfire\InputSanitizer;
use spitfire\core\annotations\ActionReflector;
use spitfire\core\Response;

/**
 * The context is a wrapper for an Intent. Basically it describes a full request
 * for a page inside Spitfire. Usually you would have a single Context in any 
 * execution.
 * 
 * Several contexts will usually only be found in Unit Tests that mock the context,
 * when using nested controllers or in a CLI application.
 * 
 * @link http://www.spitfirephp.com/wiki/index.php/Class:Context
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-10-25
 */
class Context
{
	/**
	 * This is a reference to the context itself. This is a little helper for the
	 * View and Controller objects that do expose the Context's elements via Magic
	 * methods, this way we do not need any extra cases for the context.
	 *
	 * @var Context
	 */
	public $context;
	
	/**
	 * The application running the current context. The app will provide the controller
	 * to handle the request / context provided.
	 *
	 * @var \App
	 */
	public $app;
	
	/**
	 * The controller is in charge of preparig a proper response to the request.
	 * This is the first logical level that is user-defined.
	 * 
	 * @var \Controller
	 */
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
	/**
	 *
	 * @var Request 
	 */
	public $request;
	public $response;
	
	function __construct() {
		$this->context = $this;
	}
	
	public static function create() {
		$context = new Context;
		$context->get        = new InputSanitizer($_GET);
		$context->post       = new InputSanitizer($_POST);
		$context->cache      = MemcachedAdapter::getInstance();
		$context->request    = Request::get();
		$context->parameters = new InputSanitizer($context->request->getPath()->getParameters());
		$context->response   = new Response($context);
		
		$context->app        = spitfire()->getApp($context->request->getPath()->getApp());
		$context->controller = $context->app->getController($context->request->getPath()->getController(), $context);
		$context->action     = $context->request->getPath()->getAction();
		$context->object     = $context->request->getPath()->getObject();
		
		$context->view       = $context->app->getView($context->controller);
		return $context;
	}
	
	public function run() {
		#Run the onload
		if (method_exists($this->controller, 'onload') ) {
			call_user_func_array(Array($this->controller, 'onload'), Array($this->action));
		}
		
		$reflector = new ActionReflector($this->controller, $this->action);
		$reflector->execute();
		
		#Check if the controller can handle the request
		$request = Array($this->controller, $this->action);
		if (is_callable($request)) { $_return = call_user_func_array($request, $this->object); }
		else { throw new publicException('Page not found', 404, new PrivateException('Action not found', 0)); }
		
		if ($_return instanceof Context) { return $_return; }
		else                             { return $this; }
	}
}