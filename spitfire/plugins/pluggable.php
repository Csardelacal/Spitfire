<?php

class Pluggable
{
	
	private $plugins = Array();
	
	public function attach($hook, $callable) {
		if (!isset($this->plugins[$hook])) $this->plugins[$hook] = Array();
		$this->plugins[$hook][] = $callable;
	}
	
	public function trigger($hook, $meta = null) {
		
		if (isset($this->plugins[$hook])) {
			foreach($this->plugins[$hook] as $plugin) {
				$meta = $plugin($meta);
			}
		}
		
		return $meta;
	}
	
}
