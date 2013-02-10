<?php

namespace M3W;

use App;

class testingComponent extends App
{
	
	public function helloWorld() {
		return $this->getAsset('assets/test.js');
	}

	public static function info() {
		
		\AppManager::requires('M3W', 'test');
		
		return Array(
		    'vendor' => 'M3W',
		    'name'   => 'testing',
		    'version'=> 0.1
		);
	}
	
}