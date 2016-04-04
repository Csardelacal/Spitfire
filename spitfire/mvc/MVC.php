<?php namespace spitfire\mvc;

use Pluggable;
use spitfire\core\Context;

/**
 * This class handles components common to Views, Controllers and model. Functions
 * and variables declared in this files will be accessible by any of them.
 * 
 * The MVC class provides access to many of Spitfire's shared components directly
 * via the "public" interface of the controller, models and view. Everything within
 * the context is made available to inheriting classes.
 * 
 * @property-read View $view The current view
 * @property-read \App $app The context within this is located
 * @property-read Context $context The context within this is located
 * @property-read core\Request $request The request the context is answering to
 * @property-read \spitfire\core\Response $response Contains the response body and headers
 * @property-read \Controller $controller The controller used ot handle this context
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * 
 */
class MVC extends Pluggable
{
	/**
	 * The context this element belongs to.
	 *
	 * @var Context 
	 */
	private $ctx;
	
	/**
	 * Create a new MVC base class. This class allows the components that spitfire
	 * exposes to the developer (controllers and views) to provide a comfortable
	 * way of accessing some of the more commonly used components of the system
	 * 
	 * @param Context $context
	 */
	public function __construct(Context$context) {
		$this->ctx = $context;
	}
	
	/**
	 * Provides access to the elements the context shares across the system. This
	 * allows the app to unify all the features that spitfire offers to the dev
	 * through a simple interface that is simple to use and understand.
	 * 
	 * Using get allows us to expose the contents of the context while making them
	 * read-only, which is an interesting side effect.
	 * 
	 * @param String $variable
	 * @return controller|view|DBInterface|URL|boolean
	 */
	public function __get($variable) {
		if (isset($this->ctx->$variable)) {
			return $this->ctx->$variable;
		}
		return false;
	}
	
	/**
	 * This allows the application to report the user that the controller, view and
	 * other properties this exposes are actually defined. Otherwise the code 
	 * <code>isset($this->app)</code> would return false, although app is actually
	 * accessible.
	 * 
	 * @param String $name
	 * @return boolean
	 */
	public function __isset($name) {
		switch ($name) {
			case 'controller':
			case 'view':
			case 'app':
			case 'model':
			case 'current_url':
				return true;
		}
		return false;
	}
	
}
