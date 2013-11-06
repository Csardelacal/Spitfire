<?php

namespace spitfire\io\renderers;

use \CoffeeBean;
use spitfire\io\html\HTMLTable;
use spitfire\io\html\HTMLTableRow;
use spitfire\io\html\HTMLForm;
use spitfire\io\html\HTMLInput;

class SimpleBeanRenderer extends Renderer
{
	
	public function renderForm(CoffeeBean $bean) {
		$form = new HTMLForm($this->getFormAction($bean));
		$fields = $bean->getFields();
		$renderer = new SimpleFieldRenderer();
		
		$form->addChild(new HTMLInput('hidden', '_XSS_', $bean->getXSSToken()));
		
		foreach ($fields as $field) {
			if ($field->getVisibility() == CoffeeBean::VISIBILITY_ALL || $field->getVisibility() == CoffeeBean::VISIBILITY_FORM) {
				$form->addChild($renderer->renderForm($field));
			}
		}
		
		return $form;
	}

	public function renderList(CoffeeBean $bean, $records) {
		$table = new HTMLTable();
		$renderer = new SimpleFieldRenderer();
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
				$row->putCell($renderer->renderList ($record->{$field->getModelField()}) );
			}
			//Actions
			$row->putCell(implode(' ', $this->getListActions($bean, $record)));
			
			$table->putRow($row);
		}
		return $table;
	}
	
	public function getListActions($bean, $record) {
		return Array();
	}
	
	public function getFormAction(CoffeeBean$bean) {
		return '';
	}
}