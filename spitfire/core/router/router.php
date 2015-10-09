<?php namespace spitfire\core\router;

/**
 * Routers are tools that allow your application to listen on alternative urls and
 * attach controllers to different URLs than they would normally do. Please note
 * that enabling a route to a certain controller does not disable it's canonical
 * URL.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 */
class Router extends Routable
{
	
	private $servers = Array();
	private $routes  = Array();
	
	/**
	 * This rewrites a request into a Path (or in given cases, a Response). This 
	 * allows Spitfire to use the data from the Router to accordingly find a 
	 * controller to handle the request being thrown at it.
	 * 
	 * Please note that Spitfire is 'lazy' about it's routes. Once it found a valid
	 * one that can be used to respond to the request it will stop looking for
	 * another possible rewrite.
	 * 
	 * @param string $server
	 * @param string $route
	 * @param string $method
	 * @param string $protocol
	 * @return Path|Response
	 */
	public function rewrite ($server, $route, $method, $protocol) {
		#Ensures the base server is created
		$this->server();
		#Loop through the servers to find valid routes
		$servers = $this->servers;
		
		foreach ($servers as $box) { /* @var $box Server */
			if (false !== $t = $box->rewrite($server, $route, $method, $protocol)) {
				return $t;
			}
		}
		#Implicit else.
		throw new \publicException('No such route', 404);
	}
	
	/**
	 * 
	 * @param string $address
	 * @param int    $protocol
	 * @return Server
	 */
	public function server($address = null) {
		if ($address === null && isset($_SERVER['HTTP_HOST'])) { $address = $_SERVER['HTTP_HOST']; }
		else { $address = 'localhost'; }
		
		if (isset($this->servers[$address])) { return $this->servers[$address]; }
		return $this->servers[$address] = new Server($address, $this);
	}
	
	/**
	 * Adds a new Route to the App. This redirects certain requests to a different
	 * controller than the default route would do.
	 * 
	 * @param string               $pattern
	 * @param string|closure|array $target
	 * @param int                  $method
	 * @return Route
	 */
	public function addRoute($pattern, $target, $method = 0x03, $protocol = 0x03) {
		return $this->routes[] = new Route($this, $pattern, $target, $method, $protocol);
	}
	
	/**
	 * Returns the list of routes. This is usually called by the "Server" to 
	 * retrieve generic routes
	 * 
	 * @return Route[]
	 */
	public function getRoutes() {
		return $this->routes;
	}
	
	/**
	 * Allows the router to act with a singleton pattern. This allows your app to
	 * share routes across several points of it.
	 * 
	 * @staticvar Router $instance
	 * @return Router
	 */
	public static function getInstance() {
		static $instance = null;
		if ($instance) { return $instance; }
		else           { return $instance = new Router(); }
	}

}