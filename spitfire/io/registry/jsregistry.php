<?php

namespace spitfire\registry;

class JSRegistry extends Registry
{
	public function __toString() {
		$data = $this->getData();
		$str  = '';
		
		foreach ($data as $script) {
			$str.= "<script type=\"text/javascript\" src=\"$script\" ></script>";
		}
		
		return $str;
	}
}