<?php

abstract class App
{
	
	private $name;
	static  $config;
	
	public function __construct() {
		$this->name = str_replace('Component', '', get_class($this));
	}
	
	public function getDir() {
		return AppManager::COMPONENT_DIRECTORY . str_replace('\\', '/', $this->name) . '/';
	}
	
	public function getAsset($asset) {
		return URL::make($this->getDir() . '/' . $asset);
	}
	
	/**
	 * 
	 * @return AppConfig
	 */
	public static function getConfig() {
		if (self::$config) return self::$config;
		else return self::$config = new AppConfig();
	}
	
	public static function info() {
		return Array(
		    'vendor'  => '',
		    'name'    => '',
		    'version' => 0.0
		);
	}
	
}