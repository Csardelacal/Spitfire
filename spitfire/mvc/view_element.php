<?php

class _SF_ViewElement extends _SF_MVC
{
	private $file;
	private $data;
	
	public function __construct($file, $data) {
		$this->file = $file;
		$this->data = $data;
	}
	
	public function set ($key, $value) {
		$this->data[$key] = $value;
	}
	
	public function render () {
		ob_start();
		foreach ($this->data as $k => $v) $$k = $v;
		echo '<!-- Started: ' . $this->file .' -->';
		include $this->file;
		echo '<!-- Ended: ' . $this->file .' -->';
		return ob_get_clean();
		
	}
	
	public function __toString() {
		return $this->render();
	}
}