<?php namespace spitfire\core\router;

use Closure;

/**
 * A route is a class that rewrites a URL path (route) that matches a
 * route or pattern (old_route) into a new route that the system can 
 * use (new_route) to handle the current request.
 * 
 * 
 * @todo The route needs to test for the current protocol.
 * @author César de la Cal <cesar@magic3w.com>
 */
class Route
{
	/* These constants are meant for evaluating if a request should be answered 
	 * depending on if the request is done via HTTP(S). This is especially useful
	 * when your application wants to enforce HTTPS for certain requests.
	 */
	const PROTO_HTTP    = 0x01;
	const PROTO_HTTPS   = 0x02;
	const PROTO_ANY     = 0x03;
	
	/* These constants are intended to allow routes to react differently depending
	 * on the METHOD used to issue the request the server is receiving. Spitfire
	 * accepts any of the standard GET, POST, PUT or DELETE methods.
	 */
	const METHOD_GET    = 0x01;
	const METHOD_POST   = 0x02;
	const METHOD_PUT    = 0x04;
	const METHOD_DELETE = 0x08;
	
	/**
	 * This var holds a reference to a route server (an object containing a pattern
	 * to match virtualhosts) that isolates this route from the others.
	 * 
	 * @var \spitfire\router\Server 
	 */
	private $server;
	private $pattern;
	private $new_route;
	private $parameters;
	private $method;
	private $protocol;
	
	/**
	 * A route is a pattern Spitfire uses to redirect an URL to something else.
	 * It can 'redirect' (without it being a 302) a request to a new URL, it can
	 * directly send back a response or assign a custom controller, action and 
	 * object to the request.
	 * 
	 * @param \spitfire\router\Server $server The server this route belongs to
	 * @param string $pattern
	 * @param string $new_route
	 * @param string $method
	 * @param string $pattern
	 */
	public function __construct(Server$server, $pattern, $new_route, $method, $proto = Route::PROTO_ANY) {
		$this->server    = $server;
		$this->new_route = $new_route;
		$this->method    = $method;
		$this->protocol  = $proto;
		
		$array = array_filter(explode('/', $pattern));
		array_walk($array, function (&$pattern) {$pattern= new Pattern($pattern);});
		$this->pattern = $array;
	}
	
	/**
	 * Tests all the elements of a pattern to see whether the tested route is 
	 * valid or not and to fetch the parameters for it. In case the route and the
	 * URL match we will have an array of parameters in the route that allow us
	 * to customize a request.
	 * 
	 * @throws RouteMismatchException In case the route was not valid.
	 * @param Pattern[] $pattern
	 * @param string[] $array
	 */
	protected function patternWalk($pattern, $array) {
		foreach ($pattern as $p) {
			$this->parameters = array_merge($this->parameters, $p->test(array_shift($array)));
		}
	}
	
	/**
	 * Checks whether a certain method applies to this route. The route can accept
	 * as many protocols as it wants. The protocols are converted to hex integers
	 * and are AND'd to check whether the selected protocol is included in the 
	 * list of admitted ones.
	 * 
	 * @param string|int $method
	 * @return boolean
	 */
	public function testMethod($method) {
		if (!is_numeric($method)) {
			switch ($method){
				case 'GET' :   $method = self::METHOD_GET; break;
				case 'POST':   $method = self::METHOD_POST; break;
				case 'PUT' :   $method = self::METHOD_PUT; break;
				case 'DELETE': $method = self::METHOD_DELETE; break;
			}
		}
		return $this->method & $method;
	}
	
	/**
	 * Tests if a URL matches the current Route. If so it will return true and you
	 * can use the parameters in it.
	 * 
	 * @param string $URI
	 * @return boolean
	 */
	public function testURI($URI) {
		$array = array_filter(explode('/', $URI));
		$this->parameters = $this->server->getParameters();
		
		try {
			$this->patternWalk($this->pattern, $array);
			return true;
		} catch(RouteMismatchException $e) {
			return false;
		}
	}
	
	public function testProto($protocol) {
		if (!is_int($protocol)) {
			$protocol = ($protocol && $protocol != 'off')? Route::PROTO_HTTPS : Route::PROTO_HTTP;
		}
		return $this->protocol & $protocol;
	}
	
	public function test($URI, $method, $protocol) {
		return $this->testURI($URI) && $this->testMethod($method) && $this->testProto($protocol);
	}
	
	protected function rewriteString() {
		$parameters = $this->getParameters();
		$route      = $this->new_route;
		
		foreach ($parameters as $k => $v) {
			$route = str_replace (':' . $k, $v, $route);
		}
		
		return $route;
	}
	
	protected function rewriteArray() {
		$request = \spitfire\Request::get();
		$route   = $this->new_route;
		if (isset($route['controller'])) {
			$controller = str_replace($this->getParameters(true), $this->getParameters(), $route['controller']) . 'Controller';
			$instance  = new $controller;
			$request->setController($instance);
		}
		
		if (isset($route['action'])) {
			$action = str_replace($this->getParameters(true), $this->getParameters(), $action);
			$request->setAction($action);
		}
		
		if (isset($route['object'])) {
			foreach ($route['object'] as &$o) {
				$o = str_replace($this->getParameters(true), $this->getParameters(), $action);
			}
			$request->setObject($route['object']);
		}
		
		return true;
	}
	
	public function rewrite($URI, $method, $protocol) {
		if ($this->test($URI, $method, $protocol)) {
			if (is_string($this->new_route))         {return $this->rewriteString();}
			if ($this->new_route instanceof Closure) {return call_user_func_array($this->new_route, $this->parameters);}
			if (is_array($this->new_route))          {return $this->rewriteArray(); }
		}
		return false;
	}
	
	public function getParameters($keys = false) {
		if (!$keys) return $this->parameters;
		
		$array = array_keys($this->parameters);
		array_walk($array, function(&$e) {$e = ':' . $e;});
		return $array;
	}
}