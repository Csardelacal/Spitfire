<?php namespace M3W\admin;

class DashboardModuleGroup
{
	
	private $modules = Array();
	private $parent  = null;
	
	public function __construct() {
		$args = func_get_args();
		$this->parent  = array_shift($args);
		$this->modules = $args;
	}
	
	public function putModule(DashboardModule$module) {
		$this->modules[] = $module;
		return $this;
	}
	
	public function getSpan() {
		$span = 0;
		foreach ($this->modules as $module) {
			$span += $module->getSpan();
		}
		return $span;
	}
	
	public function endGroup() {
		return $this->parent;
	}
	
	public function __toString() {
		$renderer = new DashboardModuleRenderer();
		$_return = '';
		foreach ($this->modules as $module) {$_return.= $renderer->toHTML($module);}
		
		return strval(new \spitfire\io\html\HTMLDiv($_return, Array('class' => "row{$this->getSpan()} fluid")));
	}
	
}