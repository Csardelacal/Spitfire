<?php

namespace spitfire\io\html;

abstract class HTMLUnclosedElement extends HTMLElement
{
	
	public function getChildren() {
		return null;
	}

	public function getContent() {
		return null;
	}
	
	public function __toString() {
		$tag = $this->getTag();
		$params = array_filter($this->getParams(), function ($v) {return $v !== null;});
		foreach ($params as $name => &$p) {
			if ($p === true) $p = $name;
			else $p = "$name=\"$p\"";
		}
		unset($p);
		
		return sprintf('<%s %s>', 
			$tag, implode(' ', $params));
	}
}
