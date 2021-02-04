<?php namespace app;

use spitfire\App as Prototype;

/**
 * Kernels are very powerful files, and should not be edited lightly.
 * A kernel defines middleware that will be used for ALL requests that
 * this application receives, including nested applications.
 * 
 * The kernel is also in charge of assembling an intent object, this is
 * created by receiving a request from the webserver (or the CLI) and 
 * creating an intent object that can be used to issue a response.
 * 
 */
 class App extends Prototype
 {
	 
	public $middleware = [
		
	];
	
	public function namespace() {
		return __NAMESPACE__;
	}
	
	public function directory() {
		return __DIR__;
	}

}
 