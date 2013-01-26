<?php

namespace M3W;

use Component;

class testingComponent extends Component
{
	
	public function helloWorld() {
		return $this->getAsset('assets/test.js');
	}

	public static function info() {
		
		\ComponentManager::requires('M3W', 'test');
		
		return Array(
		    'vendor' => 'M3W',
		    'name'   => 'testing',
		    'version'=> 0.1
		);
	}
	
}