<?php

namespace spitfire\router;

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
	
	public function rewrite ($server, $route, $method, $protocol) {
		foreach ($this->servers as $box) {
			foreach($box as $m) {
				if (false !== $t = $m->rewrite($server, $route, $method, $protocol)) return $t;
			}
		}
		#Implicit else.
		return $route;
	}
	
	/**
	 * 
	 * @param string $address
	 * @param int    $protocol
	 * @return Server
	 */
	public function server($address = null, $protocol = Route::PROTO_ANY) {
		if ($address === null && is_string($_SERVER['HTTP_HOST'])) { $address = $_SERVER['HTTP_HOST']; }
		if (isset($this->servers[$address][$protocol])) { return $this->servers[$address][$protocol]; }
		return $this->servers[$address][$protocol] = new Server($address, $protocol);
	}

	public function addRoute($pattern, $target, $method = 0x03) {
		return $this->server()->addRoute($pattern, $target, $method);
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
		if ($instance) return $instance;
		else return $instance = new Router();
	}

}

/*USE EXAMPLE
router::route('/* /profile', '/user/index/$1');
router::route('/* /gallery', '/gallery/index/$1');

router::rewrite('/Csardelacal/profile');
router::rewrite('/tuqiri/gallery');
/**/