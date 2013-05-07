<?php

namespace spitfire\io\html;

class HTMLTableRow extends HTMLElement
{
	private $cells;

	public function putCell($cell) {
		$this->cells[] = $cell;
	}

	public function getContent() {
		return '<td>' . implode('</td><td>', $this->cells) . '</td>';
	}

	public function getChildren() {
		return  $this->cells;
	}

	public function getParams() {
		return Array();
	}

	public function getTag() {
		return 'tr';
	}
	
}