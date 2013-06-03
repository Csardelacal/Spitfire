<?php

namespace spitfire\io\beans\renderers;

use \CoffeeBean;
use spitfire\io\html\HTMLTable;
use spitfire\io\html\HTMLTableRow;
use spitfire\io\html\HTMLTableCell;

class SimpleBeanRenderer extends Renderer
{
	
	public function renderForm(CoffeeBean $bean) {
		
	}

	public function renderList(CoffeeBean $bean, $records) {
		$table = new HTMLTable();
		//headers
		$row = new HTMLTableRow();
		foreach ($bean->getFields() as $field) {
			if ($field->getVisibility() == CoffeeBean::VISIBILITY_ALL || $field->getVisibility() == CoffeeBean::VISIBILITY_LIST)
			$row->putCell($field->getCaption());
		}
		$row->putCell('Actions');
		$table->putRow($row);
		//Content
		foreach($records as $record) {
			$row = new HTMLTableRow();
			foreach ($bean->getFields() as $field) {
				if ($field->getVisibility() == CoffeeBean::VISIBILITY_ALL || $field->getVisibility() == CoffeeBean::VISIBILITY_LIST)
				$row->putCell($record->{$field->getModelField()});
			}
			//Actions
			$row->putCell(implode(' ', $this->getActions($bean, $record)));
			
			$table->putRow($row);
		}
		return $table;
	}
	
	public function getActions($bean, $record) {
		return Array();
	}
}