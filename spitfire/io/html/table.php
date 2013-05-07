<?php

namespace spitfire\io\html;

class HTMLTable extends HTMLElement
{
	private $rows;

	public function putRow($row) {
		$this->rows[] = $row;
	}

	public function getChildren() {
		return  $this->rows;
	}

	public function getParams() {
		return Array('cellspacing' => 0);
	}

	public function getTag() {
		return 'table';
	}
	
}