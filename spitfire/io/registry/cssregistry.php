<?php

namespace spitfire\registry;

class CSSRegistry extends Registry
{
	public function __toString() {
		$data = $this->getData();
		$str  = '';
		
		foreach ($data as $css) {
			$str.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\" />";
		}
		
		return $str;
	}
}