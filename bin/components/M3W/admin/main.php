<?php

namespace M3W;

use App;
use \spitfire\AutoLoad;

class adminComponent extends App
{
	
	public function __construct() {
		parent::__construct();
		AutoLoad::registerClass('adminController', $this->getDir() . '/admin.php');
	}
	
	public static function getConfig() {
		$intent = parent::getConfig();
		return $intent;
	}

	public static function info() {
		$info = parent::info();
		$info['vendor']  = 'M3W';
		$info['name']    = 'admin';
		$info['version'] = 0.1;
		return $info;
	}
	
}