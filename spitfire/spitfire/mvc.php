<?php

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
	/**
	 * 
	 * @param String $variable
	 * @return controller|view|DBInterface|boolean
	 */
	function __get($variable) {
		
		switch ($variable) {
			case 'controller':
				return $this->controller = SpitFire::$controller;
				break;
			case 'view':
				return $this->view = Spitfire::$view;
				break;
			case 'model':
				return $this->model = SpitFire::$model;
				break;
			default:
				return false;
				break;
		}
	}
}
