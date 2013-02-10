<?php

namespace spitfire\io\html;

abstract class HTMLUnclosedElement extends HTMLElement
{
	public function getContent() {
		return null;
	}
	
	public function __toString() {
		$tag = $this->getTag();
		$params = $this->getParams();
		foreach ($params as $name => &$p) $p = "$name = \"$p\"";
		unset($p);
		
		return sprintf('<%s %s>' . "\n", 
			$tag, implode(' ', $params));
	}
}
