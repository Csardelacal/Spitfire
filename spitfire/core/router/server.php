<?php namespace spitfire\core\router;

use spitfire\Request;

class Server extends Routable
{
	
	private $router;
	private $pattern;
	private $parameters;
	private $routes = Array();
	
	public function __construct($pattern, Router$router) {
		$array = explode('.', $pattern);
		array_walk($array, function (&$pattern) {$pattern= new Pattern($pattern);});
		$this->pattern = $array;
		
		$this->router  = $router;
	}
	
	/**
	 * 
	 * @throws spitfire\router\RouteMismatchException If the path does not match
	 * @param array $pattern
	 * @param array $array
	 */
	protected function patternWalk($pattern, $array) {
		foreach ($pattern as $p) {
			$this->parameters = array_merge($this->parameters, $p->test(array_shift($array)));
		}
	}
	
	public function test($servername) {
		$array = explode('.', $servername);
		$this->parameters = Array();
		
		try {
			$this->patternWalk($this->pattern, $array);
			return true;
		} catch(RouteMismatchException $e) {
			return false;
		}
	}
	
	public function rewrite($server, $url, $method, $protocol) {
		
		if ($this->test($server)) {
			#Combine routes from the router and server
			$routes = array_merge($this->routes, $this->router->getRoutes());
			#Test the routes
			foreach ($routes as $route) {
				if (false != $rewrite = $route->rewrite($url, $method, $protocol, $this)) {
					//Request::get()->setParameters($route->getParameters());
					if (!$rewrite instanceof Path && is_string($rewrite)) {$url = $rewrite;}
					else { return $rewrite; }
				}
			}
		}
		return false;
	}
	
	public function getParameters() {
		return $this->parameters;
	}

	public function addRoute($pattern, $target, $method = 0x03, $protocol = 0x03) {
		return $this->routes[] = new Route($this, $pattern, $target, $method, $protocol);
	}

}