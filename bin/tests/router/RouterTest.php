<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use \spitfire\core\router\Route;

class RouterTest extends PHPUnit_Framework_TestCase
{
	
	public function testCreateRoute() {
		
		$router = \spitfire\core\router\Router::getInstance();
		$route  = $router->get('/test', 'test2');
		
		$this->assertInstanceOf('\spitfire\core\router\Route', $route);
	}
	
	public function testStringRoute() {
		
		$router = \spitfire\core\router\Router::getInstance();

		$route  = $router->get('/test', 'test2');
		$this->assertEquals(true, $route->test('/test', 'GET', Route::PROTO_HTTP, $router->server()));
		$this->assertEquals('/test2', $route->rewrite('/test', 'GET', Route::PROTO_HTTP, $router->server()));

		$route2 = $router->get('/another/:param', '/:param/another');
		$this->assertEquals('/test/another', $route2->rewrite('/another/test', 'GET', Route::PROTO_HTTP, $router->server()));
		$this->assertEquals(false, $route2->rewrite('/another/test', 'POST', Route::PROTO_HTTP, $router->server()));
	}
	
}