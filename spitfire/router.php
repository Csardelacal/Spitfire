<?php

/**
 * A route is a class that rewrites a URL path (route) that matches a
 * route or pattern (old_route) into a new route that the system can 
 * use (new_route)
 * @package Spitfire.router
 * @author  César de la Cal <cesar@magic3w.com>
 */
class _SF_Route
{
	private $old_route;
	private $new_route;
	
	#PUBLIC METHODS
	public function __construct($old_route, $new_route) {
		$this->old_route = $old_route;
		$this->new_route = $new_route;
	}
	
	public function rewrite($route) {
		//STEP 1: PARSE ROUTES INTO ARRAYS
		$route   = array_values(array_filter(explode('/', $route)));                       //Turn a route [/this/is/a/route] into Arrays
		$target  = array_values(array_filter(explode('/', $this->new_route)));             //Array(this, is, a, route)
		$pattern = array_values(array_filter(explode('/', $this->old_route)));             //So we can analyze the components.
		
		
		#Store the size of the route.
		$length = count($pattern);

		//STEP 2: CHECK IF THE TARGET ROUTE MATCHES THE EXPRESSION GIVEN
		//We start at 1 because 0 is always empty
		for ($pos = 0; $pos < $length; $pos++) {
			if ($pattern[$pos] == '*')                                                  //If it's a wildcard asimilate it
				$pattern[$pos] = $route[$pos];
			else if (isset($route[$pos]) && $pattern[$pos] != $route[$pos])            //Otherwise check if it matches.
				return false;				                            //By returning false we avoid further checking
		}
		
		
		//STEP 3: REPLACE $ VARIABLES
		#Store the size of the route.
		$length = count($target);

		//Loop through the new route to start rewriting.
		for($pos = 0; $pos < $length; $pos++) {
			if ($target[$pos] && $target[$pos][0] == '$') {  //Maybe it's a $XX to be replaced
				$new_pos = (int)substr($target[$pos], 1);    //Find it's new position
				$target[$pos] = $pattern[$new_pos];          //Write the value from the route into the new one.
			}
		}
		
		//Check if the source route was longer and add additional params
		$length = count($route);
		
		for ($pos = count($target); $pos < $length; $pos++) {
			$target[$pos] = $route[$pos];
		}
		
		return '/' . implode($target, '/');                        //Return the route adding the slashes
	}
}

class router
{
	
	static $routes = array();
	
	#STATIC METHODS
	static function route ($old_route, $new_route) {
		$route = new _SF_route($old_route, $new_route);
		self::$routes[] = $route;
		return $route;
	}
	
	static function rewrite ($route) {
		foreach (self::$routes as $rule)
			if (false !== $t = $rule->rewrite($route)) return $t;
		#Implicit else.
		return $route;
	}
	
	static function redirect($location) {
		header("location: $location");
		throw new publicException("Sent to $location", 301);
	}
}

/*USE EXAMPLE
router::route('/* /profile', '/user/index/$1');
router::route('/* /gallery', '/gallery/index/$1');

router::rewrite('/Csardelacal/profile');
router::rewrite('/tuqiri/gallery');
/**/