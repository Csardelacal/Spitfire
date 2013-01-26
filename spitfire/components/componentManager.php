<?php

class ComponentManager
{
	
	private static $components = Array();
	
	const COMPONENT_DIRECTORY = 'bin/components/';
	const ASSET_DIR           = 'components/';


	public static function register($vendor, $component) {
		self::$components[$vendor . '\\' . $component] = call_user_func_array(Array($vendor.'\\'.$component, 'info'), Array());
	}
	
	public static function get($p1, $p2 = false, $min_version = 0, $max_version = 0) {
		if (!$p2) {
			if (isset(self::$components[$p1])) {
				return new $p1();
			}
		} else {
			foreach(self::$components as $name => $component) {
				if ($component['vendor'] == $p1 && $component['name'] == $p2) {
					if ($max_version && $component['version'] > $max_version)  continue;
					if ($min_version && $component['version'] < $min_version)  continue;
					return new $name();
				}
			}
		}
	}
	
	public static function requires ($vendor, $name, $min_version = false, $max_version = false) {
		foreach(self::$components as $component) {
			if ($component['vendor'] == $vendor && $component['name'] == $name) {
				if ($max_version && $component['version'] > $max_version)  continue;
				if ($min_version && $component['version'] < $min_version)  continue;
				return true;
			}
		}
		throw new privateException('No valid component found to satisfy dependency');
	}
	
}