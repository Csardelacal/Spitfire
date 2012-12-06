<?php

abstract class Component
{
	
	private $name;
	
	public function __construct() {
		$this->name = str_replace('Component', '', get_class($this));
	}
	
	public function getDir() {
		return ComponentManager::COMPONENT_DIRECTORY . $this->name . '/';
	}
	
	public function getAsset($asset) {
		return URL::asset(ComponentManager::ASSET_DIR . $this->name . '/' . $asset);
	}
	
	abstract public static function info();
	
}