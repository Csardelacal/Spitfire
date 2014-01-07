<?php

namespace spitfire\io\html;

abstract class HTMLElement
{
	private $parameters = Array();
	private $children   = Array();
	
	private $buffer     = null;
	
	public abstract function getTag();
	
	public function setParameter($parameter, $value) {
		$this->parameters[$parameter] = $value;
	}
	
	public function getParams() {
		return $this->parameters;
	}
	
	public function getParam($name) {
		return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
	}
	
	public function addChild($child) {
		$this->children[] = $child;
	}

	public function getChildren() {
		return $this->children;
	}

	public function getContent() {
		$this->endBuffering();
		$children = $this->getChildren();
		
		if (count($children) == 1) return reset ($children);
		else return "\n" . implode("\n", $children) . "\n";
	}
	
	public function startBuffering() {
		$this->buffer = ob_start();
	}
	
	public function endBuffering() {
		if ($this->buffer !== null) {
			$this->addChild(ob_get_clean());
		}
	}

	public function __toString() {
		$tag = $this->getTag();
		$params = $this->getParams();
		foreach ($params as $name => &$p) $p = "$name=\"$p\"";
		unset($p);
		
		return sprintf('<%s %s>' . '%s' . '</%s>', 
			$tag, implode(' ', $params), $this->getContent(), $tag);
	}
}
