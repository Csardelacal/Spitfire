<?php namespace spitfire;

use Pluggable;

/**
 * This class handles components common to Views, Controllers and model. Functions
 * and variables declared in this files will be accessible by any of them.
 * 
 * @property spitfire\View $view The current view
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @package Spitfire.spitfire
 * 
 */

class MVC extends Pluggable
{
	public $context;
	
	function __construct(Context$context) {
		$this->context = $context;
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
		if (isset($this->context->$variable)) {
			return $this->context->$variable;
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
