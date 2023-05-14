<?php namespace app\services;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use spitfire\core\service\Provider;

/* 
 * Copyright (C) 2021 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

class LoggingServiceProvider extends Provider
{
	
	/**
	 * The logger is a bit of an unconventional provider, because it does a lot that
	 * I would personally consider init in the register method.
	 * 
	 * Here's the reason why:
	 * 
	 * * The provider does not actually init anything else, it does not depend on anything
	 *   that needs to be registered by another application to work.
	 * 
	 * * Logging is ubiquitous, which means that we'd be best prepared for when all the other
	 *   providers wish to start initializing their stuff and start issuing debug statements.
	 */
	public function register(ContainerInterface $container) : void
	{
		/**
		 * Our application needs to be aware whether it is in debugging mode or not,
		 * depending this we will enable additional logging, makign it easier and safer
		 * to debug the application.
		 */
		$debug = config('app.debug', true);
		
		/**
		 * Start monolog, the names in here seem
		 */
		$monolog = new Logger($debug? 'development' : 'production');
		
		/**
		 * Set up monolog with basic logging within the command line, here we will make use
		 * of the stderr streams to inform the user right away about issues.
		 * 
		 * If you have debugging disabled, by default only information that is warnings will
		 * be printed to the screen, allowing the user to ingore debugging statements.
		 */
		if (cli()) {
			$monolog->pushHandler(new StreamHandler(STDERR, $debug? Logger::DEBUG : Logger::WARNING));
		}
		
		/**
		 * In debugging mode, we will enable more aggressive logging, and make sure that we 
		 * also send debugging information to the browser.
		 */
		elseif ($debug) {
			$monolog->pushHandler(new StreamHandler(spitfire()->locations()->storage('app.log')));
			$monolog->pushHandler(new FirePHPHandler());
		}
		
		/**
		 * In production mode, we should avoid logging too aggressively (otherwise we may crash
		 * a server) and we will not print the errors to the screen.
		 */
		else {
			$monolog->pushHandler(new StreamHandler(spitfire()->locations()->storage('app.log'), Logger::ERROR));
		}
		
		/**
		 * Finally, register our handler with the service container so we can use it at the app's
		 * convenience.
		 */
		spitfire()->provider()->set(LoggerInterface::class, $monolog);
	}
	
	public function init(ContainerInterface $container) : void
	{
	}
}
