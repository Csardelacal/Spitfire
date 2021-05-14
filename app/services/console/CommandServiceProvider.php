<?php namespace app\services\console;

use spitfire\App;
use spitfire\service\Provider;
use spitfire\console\ConsoleKernel;

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

class CommandProvider extends Provider
{
	
	
	public function register()
	{
		#This provider actually just loads routes and does not register any services
	}

	public function init()
	{
		
		/**
		 * If the kernel is not a console kernel, we are executing from a webserver and 
		 * therefore there is no need to initialize the routes.
		 */
		if (!(spitfire()->kernel() instanceof ConsoleKernel)) {
			return;
		}
		
		/**
		 * Get the cluster to see which applications are loaded and which we can import commands
		 * from.
		 */
		$cluster = spitfire()->cluster();
		
		/**
		 * Load the commands for each application that we do have loaded in the cluster.
		 * 
		 * Note: We do not perform any caching here, it really makes no sense to over-optimize
		 * this section of spitfire since it only is run in the command line, which will not
		 * experience the barrage of requests that the web interface will, and we would rather
		 * have it behave consistently if something is broken.
		 * 
		 * Note: This code does not include a 'if file_exists' so the code will fail if the file
		 * does not exist or is not properly 
		 */
		foreach ($cluster->all() as $app) {
			assert($app instanceof App);
			
			/**
			 * When working with apps, they have a scoped router, which allows the application to
			 * respond to URLs that are within it's scope.
			 * 
			 * Similarly, the command provider will scope commands to the url prefix, preventing 
			 * collissions when sharing command names accross apps.
			 */
			$scope = $app->url()->getScope();
			(include_once $app->directory() . 'config/commands.php')(trim('.', str_replace('/', '.', $scope)) . '.');
		}
	}
	
}
