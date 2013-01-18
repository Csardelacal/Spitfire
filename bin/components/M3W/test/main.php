<?php


class M3W_testComponent extends Component
{
	
	public function helloWorld() {
		echo 'Hello world ' . $this->getDir();
	}

	public static function info() {
		$info = parent::info();
		$info['vendor']  = 'M3W';
		$info['name']    = 'test';
		$info['version'] = 0.1;
		return $info;
	}
	
}