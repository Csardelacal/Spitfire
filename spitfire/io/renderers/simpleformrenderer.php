<?php

namespace spitfire\io\renderers;

use \CoffeeBean;
use spitfire\io\html\HTMLTable;
use spitfire\io\html\HTMLTableRow;
use spitfire\io\html\HTMLForm;

class SimpleFormRenderer extends Renderer
{
	
	public function renderForm(RenderableForm$renderable, $errors = Array()) {
		$form = new HTMLForm($this->getFormAction($renderable));
		$fields = $renderable->getFormFields();
		$renderer = new SimpleFieldRenderer();
		
		foreach ($fields as $field) {
			if ($field->getVisibility() & CoffeeBean::VISIBILITY_FORM) {
				$form->addChild($renderer->renderForm($field, $this->getErrorsFor($field, $errors)));
			}
		}
		return $form;
	}
	
	public function getErrorsFor($field, $errors) {
		foreach ($errors as $e) {
			if ($e->getSrc() === $field) {
				return $e;
			}
		}
		return null;
	}

	public function renderList(RenderableForm$renderable, $records) {
		$table = new HTMLTable();
		$renderer = new SimpleFieldRenderer();
		//headers
		$row = new HTMLTableRow();
		foreach ($renderable->getFormFields() as $field) {
			if ($field->getVisibility() & CoffeeBean::VISIBILITY_LIST)
				{$row->putCell($this->stringifyHeader($field));}
		}
		$row->putCell('Actions');
		$table->putRow($row);
		//Content
		foreach($records as $record) {
			$row = new HTMLTableRow();
			foreach ($renderable->getFormFields() as $field) {
				if ($field->getVisibility() & CoffeeBean::VISIBILITY_LIST)
				$row->putCell($renderer->renderList ($record->{$field->getModelField()}) );
			}
			//Actions
			$row->putCell(implode(' ', $this->getListActions($renderable, $record)));
			
			$table->putRow($row);
		}
		return $table;
	}
	
	public function stringifyHeader($field) {
		return $field->getCaption();
	}
	
	public function getListActions($bean, $record) {
		return Array();
	}
	
	public function getFormAction(CoffeeBean$bean) {
		return '';
	}
}