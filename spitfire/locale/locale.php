<?php

class Locale
{
	
	private $_current_output;
	
	public function say() {
		$args = func_get_args();
		
		if (isset($this->{$args[0]})) {
			$msg = $this->{$args[0]};
			if (is_array($msg)) {
				switch($args[1]) {
					case 0:  $msg = $msg[0]; break;
					case 1:  $msg = $msg[1]; break;
					default: $msg = $msg[2]; break;
				}
			}
			
			$args[0] = $msg;
		}
		else return $args[0];
		
		return call_user_func_array('sprintf', $args);
		
	}
	
	public function getLangCode() {
		return '';
	}
	
	public function getCurrency() {
		return '$';
	}
	
	public function convertCurrency($original) {
		return $original;
	}
	
	public function start($lang) {
		if ($this->_current_output) $this->end();
		$this->_current_output = $lang;
		ob_start();
	}
	
	public function end() {
		$msg = ob_get_clean();
		if ($this->_current_output == $this->getLangCode()) echo $msg;
		$this->_current_output = null;
	}
	
}