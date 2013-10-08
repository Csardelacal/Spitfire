<?php namespace spitfire\router;

use Closure;
use spitfire\Request;

class Server extends Routable
{
	
	private $pattern;
	private $parameters;
	private $routes = Array();
	
	public function __construct($pattern) {
		$array = explode('.', $pattern);
		array_walk($array, function (&$pattern) {$pattern= new Pattern($pattern);});
		$this->pattern = $array;
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
			if ($this->pattern instanceof Closure) {$this->parameters = call_user_func($this->pattern);}
			$this->patternWalk($this->pattern, $array);
			return true;
		} catch(RouteMismatchException $e) {
			return false;
		}
	}
	
	public function rewrite($server, $url) {
		
		if ($this->test($server)) {
			foreach ($this->routes as $route) {
				if (false != $rewrite = $route->rewrite($url)) {
					Request::get()->setParameters($route->getParameters());
					return $rewrite;
				}
			}
		}
		return false;
	}
	
	public function getParameters() {
		return $this->parameters;
	}

	public function addRoute($pattern, $target, $method = 0x03) {
		return $this->routes[] = new Route($this, $pattern, $target, $method);
	}

}