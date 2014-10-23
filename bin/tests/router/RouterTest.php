<?php namespace tests\spitfire\core\router\Route;

/* 
 * This file helps testing the basic functionality of Spitfire's router. It will
 * check that rewriting basic strings and Objects will work properly.
 */

use spitfire\core\router\Route;
use spitfire\core\router\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
	
	private $router;
	
	public function setUp() {
		$this->router = new Router();
	}
	
	/**
	 * Tests the creation of routes. This will just request the router to create
	 * a route and verify that the returned value is a Route and not something 
	 * else.
	 */
	public function testCreateRoute() {
		
		$route  = $this->router->get('/test', 'test2');
		$this->assertInstanceOf('\spitfire\core\router\Route', $route);
	}
	
	/**
	 * This method tests the different string rewriting options that Spitfire 
	 * will provide you with when creating routes.
	 */
	public function testStringRoute() {
		
		$router = $this->router;
		
		#Prepare a route that redirects with no parameters
		$route  = $router->get('/test', 'test2');
		$this->assertEquals(true, $route->test('/test', 'GET', Route::PROTO_HTTP, $router->server()));
		$this->assertEquals('/test2', $route->rewrite('/test', 'GET', Route::PROTO_HTTP, $router->server()));
		$this->assertEquals(false, $route->rewrite('/test', 'POST', Route::PROTO_HTTP, $router->server()));
			//> This last test should fail because we're sending a POST request to a GET route
		
		#Prepare a route that redirects with parameters
		$route2 = $router->get('/another/:param', '/:param/another');
		$this->assertEquals('/test/another', $route2->rewrite('/another/test', 'GET', Route::PROTO_HTTP, $router->server()));
		$this->assertEquals(false, $route2->rewrite('/another/test', 'POST', Route::PROTO_HTTP, $router->server()));
	}
	
	public function testArrayRoute() {
		$router = $this->router;
		
		#Rewrite a parameter based URL into an array
		$route = $router->get('/:param1/:param2', Array('controller' => 'param1', 'action' => 'param2'));
		
		#Test whether matching works for the array string
		$this->assertEquals(true, $route->test('/another/test', 'GET', Route::PROTO_HTTP, $router->server()));
		
		#Test if the route returns a Path object
		$this->assertInstanceOf('\spitfire\core\Path', $route->rewrite('/another/test', 'GET', Route::PROTO_HTTP, $router->server()));
		#Test if the server returns a Patch object
		$this->assertInstanceOf('\spitfire\core\Path', $router->server()->rewrite('localhost', '/another/test', 'GET', Route::PROTO_HTTP));
		
		#Test if the rewriting succeeded and the data was written in the right spot
		$path  = $router->rewrite('localhost', '/another/test', 'GET', Route::PROTO_HTTP);
		$this->assertEquals('another', $path->getController());
		$this->assertEquals('test',    $path->getAction());
	}
	
}