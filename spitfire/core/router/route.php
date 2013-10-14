<?php namespace spitfire\router;

use Closure;

/**
 * A route is a class that rewrites a URL path (route) that matches a
 * route or pattern (old_route) into a new route that the system can 
 * use (new_route)
 * @author  César de la Cal <cesar@magic3w.com>
 */
class Route
{
	const PROTO_HTTP    = 0x01;
	const PROTO_HTTPS   = 0x02;
	
	const METHOD_GET    = 0x01;
	const METHOD_POST   = 0x02;
	const METHOD_PUT    = 0x04;
	const METHOD_DELETE = 0x08;
	
	private $server;
	private $pattern;
	private $new_route;
	private $parameters;
	private $method;
	private $protocol;
	
	#PUBLIC METHODS
	public function __construct($server, $pattern, $new_route, $method) {
		$this->server    = $server;
		$this->new_route = $new_route;
		$this->method    = $method;
		
		$array = array_filter(explode('/', $pattern));
		array_walk($array, function (&$pattern) {$pattern= new Pattern($pattern);});
		$this->pattern = $array;
	}
	
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
	
	public function test($URI, $method) {
		return $this->testURI($URI) && $this->testMethod($method);
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
		
		if (isset($route['obect'])) {
			foreach ($route['object'] as &$o) {
				$o = str_replace($this->getParameters(true), $this->getParameters(), $action);
			}
			$request->setObject($object);
		}
		
		return true;
	}
	
	public function rewrite($URI, $method) {
		if ($this->test($URI, $method)) {
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