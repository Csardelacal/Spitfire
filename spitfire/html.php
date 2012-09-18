<?php

class html_node
{
	private $tag;
	private $autoclosing;
	private $content = Array();
	private $attr;
	private $parentNode;
	
	public function __construct($tag, $attr = Array(), $content = Array(), $autoclosing = false) {
		
		$this->tag = $tag;
		$this->attr = $attr;
		$this->autoclosing = $autoclosing;
		
		if (is_array($content)) foreach($content as $v) $this->append($v);
		else $this->content[] = $content;
		
		if (is_array($attr))    foreach($attr as $k => $v) $this->attr[$k] = $v;
		
	}
	
	public function append($node) {
		$this->content[] = $node;
		return $node;
	}
	
	public function setAttr($attr, $val) {
		$this->attr[$attr] = $val;
	}
	
	public function __toString() {
		
		$ret = "";
		$ret.= '<' . $this->tag . ' ';
		foreach ($this->attr as $attr => $val) $ret.= "$attr = \"$val\" ";
		if ($this->autoclosing) return $ret. '/>';
		else $ret.= '>';
		
		foreach($this->content as $child) $ret.= $child;
		
		return $ret . "</{$this->tag}>\n";
	}
	
}