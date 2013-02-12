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
		$path = array_filter(explode('/', $path_info));

		/* If the path is empty it means that no parameters were given
		 * so Spitfire doesn't need to work parsing the URL. Otherwise
		 * it will retrieve data about it.
		 */
		if (!empty($path))
		{
			/* To fetch the extension requested by the user we do the
			 * following:
			 * * We get the last element of the path.
			 * * Split it by the .
			 * * Keep the first part as filename
			 * * And the rest as extension.
			 */
			$last      = explode('.', array_pop($path));
			$extension = (isset($last[1]))? $last[1] : false;
			array_push($path, $last[0]);
			
			/* To get the controller and action of an element we 
			 * keep checking if each element is a valid controller,
			 * once it didn't find a valid controller it stops.
			 */
			do {
				$controller[] = array_shift($path);
			} while (class_exists ( implode('\\', $controller) . 'Controller'));
			
			$action = array_pop($controller);
			$object = $path;
			
		
		}
		else {
			$controller = null;
			$action = null;
			$object = null;
		}
		
		

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