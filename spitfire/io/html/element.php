<?php

namespace spitfire\io\html;

abstract class HTMLElement
{
	public abstract function getTag();
	public abstract function getParams();
	public abstract function getChildren();

	public function getContent() {
		return implode("\n", $this->getChildren());
	}

	public function __toString() {
		$tag = $this->getTag();
		$params = $this->getParams();
		foreach ($params as $name => &$p) $p = "$name = \"$p\"";
		unset($p);
		
		return sprintf('<%s %s>' . "\n" . '%s' . "\n" . '</%s>' . "\n", 
			$tag, implode(' ', $params), $this->getContent(), $tag);
	}
}
