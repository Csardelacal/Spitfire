<?php

namespace spitfire\router;

class Router extends Routable
{
	
	private $servers = Array();
	private $greedy  = true;
	
	static function rewrite ($route) {
		foreach (self::$routes as $rule)
			if (false !== $t = $rule->rewrite($route)) return $t;
		#Implicit else.
		return $route;
	}
	
	public function server($address = null) {
		if ($address === null && is_string($_SERVER['HTTP_HOST'])) { $address = $_SERVER['HTTP_HOST']; }
		if (isset($this->servers[$address])) { return $this->servers[$address]; }
		return $this->servers[$address] = new Server($address);
	}

	public function addRoute($pattern, $target, $method = 0x03) {
		return $this->server()->addRoute($pattern, $target, $method);
	}

}

/*USE EXAMPLE
router::route('/* /profile', '/user/index/$1');
router::route('/* /gallery', '/gallery/index/$1');

router::rewrite('/Csardelacal/profile');
router::rewrite('/tuqiri/gallery');
/**/