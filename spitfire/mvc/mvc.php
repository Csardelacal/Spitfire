<?php

use spitfire\SpitFire;
use spitfire\Intent;

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

class _SF_MVC extends Pluggable
{
	public $intent;
	
	function __construct(Intent$intent) {
		$this->intent = $intent;
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
				return $this->view = $this->intent->getView();
				break;
			case 'model':
				return $this->model = SpitFire::$model;
				break;
			case 'app':
				return $this->model = $this->intent->getApp();
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
