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
	 * @var \spitfire\Context 
	 */
	private $ctx;
	
	function __construct(Context$context) {
		$this->ctx = $context;
	}
	
	function getApp() {
		return $this->app;
	}
	
	/**
	 * 
	 * @param String $variable
	 * @return controller|view|DBInterface|URL|boolean
	 */
	function __get($variable) {
		if (isset($this->ctx->$variable)) {
			return $this->ctx->$variable;
		}
		return false;
	}
	
	/**
	 * 
	 * @param String $name
	 * @return boolean
	 */
	function __isset($name) {
		switch ($name) {
			case 'controller':
			case 'view':
			case 'model':
			case 'current_url':
				return true;
		}
		return false;
	}
	
}
