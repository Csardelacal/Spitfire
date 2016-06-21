<?php namespace spitfire\core\router;

/**
 * The routable class is the base class for both routers and router servers, 
 * which can both accept routes and store them. In order to do so, there is a 
 * predefined set of functions that are meant to be kept the way they are and 
 * a single abstract one that adds the route to the element the way this one 
 * decides.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @last-revision 2013-10-18
 */
abstract class Routable
{
	/**
	 * This method adds a route for any request that is sent either via GET or POST
	 * this are the most standard and common behaviors and the ones recommended
	 * over PUT or DELETE due to it's behavior.
	 * 
	 * @param string $pattern A route valid pattern @link http://www.spitfirephp.com/wiki/index.php/Router/Patterns
	 * @param string|string[]|Closure $target  Where the content will be redirected to @link http://www.spitfirephp.com/wiki/index.php/Router/Target
	 * @return \spitfire\router\Route The route that is generated by this
	 */
	public function request($pattern, $target) {
		return $this->addRoute($pattern, $target, Route::METHOD_GET|Route::METHOD_POST);
	}
	
	/**
	 * This method adds a route that will only rewrite the request if it's method 
	 * is GET. Otherwise the request will be ignored.
	 * 
	 * @param string $pattern A route valid pattern @link http://www.spitfirephp.com/wiki/index.php/Router/Patterns
	 * @param string $target  Where the content will be redirected to @link http://www.spitfirephp.com/wiki/index.php/Router/Target
	 * @return \spitfire\router\Route The route that is generated by this
	 */
	public function get($pattern, $target) {
		return $this->addRoute($pattern, $target, Route::METHOD_GET);
	}
	
	
	/**
	 * This method adds a route that will only rewrite the request if it's method 
	 * is PUT. Otherwise the request will be ignored.
	 * 
	 * @param string $pattern A route valid pattern @link http://www.spitfirephp.com/wiki/index.php/Router/Patterns
	 * @param string $target  Where the content will be redirected to @link http://www.spitfirephp.com/wiki/index.php/Router/Target
	 * @return \spitfire\router\Route The route that is generated by this
	 */
	public function put($pattern, $target) {
		return $this->addRoute($pattern, $target, Route::METHOD_PUT);
	}
	
	
	/**
	 * This method adds a route that will only rewrite the request if it's method 
	 * is DELETE. Otherwise the request will be ignored.
	 * 
	 * @param string $pattern A route valid pattern @link http://www.spitfirephp.com/wiki/index.php/Router/Patterns
	 * @param string $target  Where the content will be redirected to @link http://www.spitfirephp.com/wiki/index.php/Router/Target
	 * @return \spitfire\router\Route The route that is generated by this
	 */
	public function delete($pattern, $target) {
		return $this->addRoute($pattern, $target, Route::METHOD_DELETE);
	}
	
	
	/**
	 * This method adds a route that will only rewrite the request if it's method 
	 * is POST. Otherwise the request will be ignored.
	 * 
	 * @param string $pattern A route valid pattern @link http://www.spitfirephp.com/wiki/index.php/Router/Patterns
	 * @param string $target  Where the content will be redirected to @link http://www.spitfirephp.com/wiki/index.php/Router/Target
	 * @return spitfire\router\Route The route that is generated by this
	 */
	public function post($pattern, $target) {
		return $this->addRoute($pattern, $target, Route::METHOD_POST);
	}
	
	/**
	 * This method adds a route to the current object. You can use this to customize
	 * a certain set of methods for the route to rewrite.
	 * 
	 * @param string $pattern A route valid pattern @link http://www.spitfirephp.com/wiki/index.php/Router/Patterns
	 * @param string $target  Where the content will be redirected to @link http://www.spitfirephp.com/wiki/index.php/Router/Target
	 * @param int    $method  The current method, default: Route::METHOD_GET | Route::METHOD_POST
	 * @return spitfire\router\Route The route that is generated by this
	 */
	abstract public function addRoute($pattern, $target, $method = 0x03, $protocol = 0x03);
}