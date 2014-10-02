<?php namespace tests\spitfire\core\router\Route;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use spitfire\core\router\Route;
use spitfire\core\router\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
	
	public function testCreateRoute() {
		
		$router = Router::getInstance();
		$route  = $router->get('/test', 'test2');
		
		$this->assertInstanceOf('\spitfire\core\router\Route', $route);
	}
	
	/**
	 * This method tests the different string rewriting options that Spitfire 
	 * will provide you with when creating routes.
	 */
	public function testStringRoute() {
		
		$router = new Router();

		$route  = $router->get('/test', 'test2');
		$this->assertEquals(true, $route->test('/test', 'GET', Route::PROTO_HTTP, $router->server()));
		$this->assertEquals('/test2', $route->rewrite('/test', 'GET', Route::PROTO_HTTP, $router->server()));
		$this->assertEquals(false, $route->rewrite('/test', 'POST', Route::PROTO_HTTP, $router->server()));

		$route2 = $router->get('/another/:param', '/:param/another');
		$this->assertEquals('/test/another', $route2->rewrite('/another/test', 'GET', Route::PROTO_HTTP, $router->server()));
		$this->assertEquals(false, $route2->rewrite('/another/test', 'POST', Route::PROTO_HTTP, $router->server()));
	}
	
	public function testArrayRoute() {
		$router = new Router();
		
		$route = $router->get('/:param1/:param2', Array('controller' => 'param1', 'action' => 'param2'));
		$this->assertEquals(true, $route->test('/another/test', 'GET', Route::PROTO_HTTP, $router->server()));
		
		$this->assertInstanceOf('\spitfire\core\Path', $route->rewrite('/another/test', 'GET', Route::PROTO_HTTP, $router->server()));
		$this->assertInstanceOf('\spitfire\core\Path', $router->server()->rewrite('localhost', '/another/test', 'GET', Route::PROTO_HTTP));
		
		$path  = $router->rewrite('localhost', '/another/test', 'GET', Route::PROTO_HTTP);
		
		$this->assertEquals('another', $path->getController());
		$this->assertEquals('test',    $path->getAction());
	}
	
}