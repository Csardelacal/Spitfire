<?php

namespace spitfire\io\html;

class HTMLTable extends HTMLElement
{
	private $rows;

	public function putRow($row) {
		$this->rows[] = $row;
	}

	public function getContent() {
		return implode('', $this->rows);
	}

	public function getParams() {
		return Array();
	}

	public function getTag() {
		return 'table';
	}
	
}