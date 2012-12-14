<?php

abstract class Component
{
	
	private $name;
	
	public function __construct() {
		$this->name = str_replace('Component', '', get_class($this));
	}
	
	public function getDir() {
		return ComponentManager::COMPONENT_DIRECTORY . str_replace('_', '/', $this->name) . '/';
	}
	
	public function getAsset($asset) {
		return URL::asset(ComponentManager::ASSET_DIR . str_replace('_', '/', $this->name) . '/' . $asset);
	}
	
	public static function info() {
		return Array(
		    'vendor'  => '',
		    'name'    => '',
		    'version' => 0.0
		);
	}
	
}