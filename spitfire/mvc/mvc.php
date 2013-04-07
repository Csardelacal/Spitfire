<?php

use spitfire\SpitFire;

/**
 * This class handles components common to Views, Controllers and model. Functions
 * and variables declared in this files will be accessible by any of them.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @package Spitfire.spitfire
 * 
 */

class _SF_MVC
{
	protected $app;
	
	function __construct($app) {
		$this->app = $app;
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
		
		switch ($variable) {
			case 'controller':
				return $this->controller = $this->app->controller;
				break;
			case 'view':
				return $this->view = $this->app->view;
				break;
			case 'model':
				return $this->model = SpitFire::$model;
				break;
			case 'current_url':
				return $this->current_url = spitfire()->getRequest();
			default:
				return false;
				break;
		}
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
