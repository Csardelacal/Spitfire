<?php namespace spitfire\core\router;

use spitfire\core\Path;

/**
 * A server in Spitfire's router is a certain virtual host the application is 
 * listening to. Imagine your application replying to different domain names with
 * different responses.
 * 
 * This allows a Spitfire based application to, for example, manage all the 
 * GTLD for your application like yourapp.com, yourapp.es or yourapp.de
 */
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
		$parameters = Array();
		
		foreach ($pattern as $p) {
			$parameters = array_merge($parameters, $p->test(array_shift($array)));
		}
		
		$this->parameters = new Parameters();
		$this->parameters->addParameters($parameters);
	}
	
	public function test($servername) {
		$array = explode('.', $servername);
		
		try {
			$this->patternWalk($this->pattern, $array);
			return true;
		} catch(RouteMismatchException $e) {
			return false;
		}
	}
	
	public function rewrite($server, $url, $method, $protocol) {
		#If the server doesn't match we don't continue
		if (!$this->test($server)) { return false; }
		
		#Combine routes from the router and server
		$routes = array_merge($this->routes, $this->router->getRoutes());
		#Test the routes
		foreach ($routes as $route) {
			$rewrite = $route->rewrite($url, $method, $protocol, $this);

			if ( ($rewrite instanceof Path)) { return $rewrite; }
			if ( $rewrite !== false)         { $url = $rewrite; }
		}
	}
	
	public function getParameters() {
		if ($this->parameters === null) { $this->test(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'); }
		if ($this->parameters === false) { return new Parameters(); }
		return $this->parameters;
	}

	public function addRoute($pattern, $target, $method = 0x03, $protocol = 0x03) {
		return $this->routes[] = new Route($this, $pattern, $target, $method, $protocol);
	}

}