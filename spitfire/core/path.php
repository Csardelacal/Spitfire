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
		
		$request = new Request();

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
			$info      = array_pop($last);
			$extension = (!empty($info))? $info : 'php';
			array_push($path, implode('.', $last));
			
			/* Try to get the current namespace, if one is registered
			 * we will redirect the request to another app.
			 */
			if (spitfire()->appExists(reset($path))) {
				$namespace = array_shift($path);
			}
			else $namespace = '';
			
			$app = spitfire()->getApp($namespace);
			$request->setApp($app);
			
			/* To get the controller and action of an element we 
			 * keep checking if each element is a valid controller,
			 * once it didn't find a valid controller it stops.
			 */
			if ($app->hasController(reset($path))){
				$controllerName = array_shift($path);
				$controller = $app->getController($controllerName);
			}
			else{
				$controller = $app->getController(environment::get('default_controller'));
				$controllerName = environment::get('default_controller');
			}
			
			if (is_callable(Array($controller, reset($path)))) {
				$action = array_shift($path);
			}
			elseif (!reset($path)) {
				$action = environment::get('default_action');
			}
			else {
				throw new \publicException('Action not Found', 404);
				$action = 'detail';
			}
			
			$object = $path;
		
		}
		else {
			$app        = spitfire();
			$controllerName = environment::get('default_controller');
			$controller = $app->getController(environment::get('default_controller'));
			$action     = environment::get('default_action');
			$object     = Array();
			$extension  = 'php';
		}
		
		$request->setApp($app);
		$request->setController($controller);
		$request->setControllerURI($controllerName);
		$request->setAction($action);
		$request->setObject($object);
		$request->setExtension($extension);

		if (substr($action, 0,1) == '_' || $action == 'onload') 
			throw new publicException('E_PAGE_NOT_FOUND', 404);

		
		return $request;
	}
	
}