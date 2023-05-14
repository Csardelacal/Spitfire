<?php namespace app\services\router;

use Psr\Container\ContainerInterface;
use spitfire\core\router\Router;
use spitfire\core\service\Provider;

/* 
 * The MIT License
 *
 * Copyright 2021 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class RouteProvider extends Provider
{
	
	
	public function register(ContainerInterface $container) : void
	{
		#This provider actually just loads routes and does not register any services
	}
	
	public function init(ContainerInterface $container) : void
	{
		/*
		 * Locate the current application's root directory. This is a quirky way
		 * of finding the file, but it's a requirement, since composer won't let us
		 * know where we are, and what the package root is.
		 */
		$approot = realpath(__DIR__ . '/../../../');
		
		/*
		 * We also need access to the router
		 */
		$router = $container->get(Router::class);
		
		/*
		 * Within this, we want to load our two routes files, routes.web and routes.api
		 * 
		 * Please note that we expect these files to return a closure that accepts
		 * the router as parameter and then can pass our scoped router to these closures
		 * so they append the routes to the appropriate url spaces.
		 */
		(include_once $approot . '/resources/routes.api.php')($router);
		(include_once $approot . '/resources/routes.web.php')($router);
	}
}
