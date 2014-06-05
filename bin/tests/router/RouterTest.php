<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RouterTest extends PHPUnit_Framework_TestCase
{
	
	public function testCreateRoute() {
		
		$router = \spitfire\core\router\Router::getInstance();
		$route  = $router->get('/test', 'test2');
		
		$this->assertInstanceOf('\spitfire\core\router\Route', $route);
	}
	
	public function testRoute() {
		
		$router = \spitfire\core\router\Router::getInstance();
		$route  = $router->get('/test', 'test2');
		
		$this->assertEquals($route->test(true, '/test', 'GET', \spitfire\core\router\Route::PROTO_HTTP));
		$this->assertEquals($route->rewrite('/test2', '/test', 'GET', \spitfire\core\router\Route::PROTO_HTTP));
	}
	
}