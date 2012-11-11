<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class _SF_MVC
{
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
