<?php

namespace spitfire;

use router;
use URL;

class Path
{
	
	static $current_url;

	/**
	 * getPath()
	 * Reads the current path the user has selected and tries to detect
	 * Controllers, actions and objects from it.
	 * 
	 * [NOTICE] getPath does not guarantee safe input, you will have to
	 * manually check whether the input it received is valid.
	 * 
	 * [NOTICE] getPath will prevent users from accessing any controllers
	 * when in maintenance mode. But will also throw an error when the
	 * user tries to enter maintenance mode when in normal operation.
	 * This means you need to use a separate controller for generating
	 * WiP sites, or manually edit it everytime you enter service mode.
	 */
	public static function getPath() {

		/** @var $path_info string */
		$path_info = router::rewrite($_SERVER['PATH_INFO']);
		$path = explode('/', $path_info);
		if ( empty($path[0]) ) array_shift ($path);

		//Try to fetch the extension
		$last      = explode('.', end($path));
		$extension = (isset($last[1]))? $last[1] : false;

		if ($extension) {
			array_pop($path);
			array_push($path, $last[0]);
		}

		//Assign the object as array (with multiple elements) to a Global
		$controller_data = Array();
		if (isset($path[0]) && $path[0]) {
			do {
				$controller_data[] = array_shift($path); 
			}while (class_exists(implode('_', $controller_data) . 'Controller'));
		}

		if (isset($controller_data[0]) )
			array_unshift($path, array_pop($controller_data));

		$controller = $controller_data;
		$action     = array_shift($path);
		$object     = $path;

		//Check if invalid url's are being requested
		if ($controller == environment::get('maintenance_controller') ) 
			throw new publicException('User tried to access maintenance mode', 401);
		if (substr($action, 0,1) == '_' || $action == 'onload') 
			throw new publicException('E_PAGE_NOT_FOUND', 404);

		//Define controllers
		$url = new URL($controller, $action, $object);
		$url->setExtension($extension);
		self::$current_url = $url;
		return $url;
	}
	
}