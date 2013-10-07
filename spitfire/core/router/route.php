<?php namespace spitfire\router;

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
	
	public function test($URI) {
		$array = array_filter(explode('/', $URI));
		$this->parameters = $this->server->getParameters();
		
		try {
			$this->patternWalk($this->pattern, $array);
			return true;
		} catch(RouteMismatchException $e) {
			return false;
		}
	}
	
	public function getParameters() {
		return $this->parameters;
	}
}