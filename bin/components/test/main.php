<?php

class testComponent extends Component
{
	
	public function helloWorld() {
		echo 'Hello world ' . $this->getDir();
	}

	public static function info() {
		return Array(
		    'vendor' => 'M3W',
		    'name'   => 'test',
		    'version'=> 0.1
		);
	}
	
}