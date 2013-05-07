<?php

namespace spitfire\registry;

use ArrayAccess;

class Registry implements ArrayAccess
{
	private $data = Array();
	
	public function getData() {
		return $this->data;
	}
	
	public function add($val) {
		if (!in_array($val, $this->data)) $this->data[] = $val;
	}
	
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset) {
		if (isset($this->data[$offset])) return $this->data[$offset];
	}

	public function offsetSet($offset, $value) {
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}
	
	public function __invoke($val) {
		if (!in_array($val, $this->data)) $this->data[] = $val;
	}
}
