<?php

namespace spitfire\io\renderers;

use \CoffeeBean;
use spitfire\io\html\HTMLTable;
use spitfire\io\html\HTMLTableRow;
use spitfire\io\html\HTMLForm;

class SimpleFormRenderer extends Renderer
{
	private $field_renderer;
	
	public function __construct($fieldrenderer = null) {
		$this->field_renderer = ($fieldrenderer === null)? new SimpleFieldRenderer() : $fieldrenderer;
	}
	
	public function renderForm(RenderableForm$renderable) {
		$form = new HTMLForm($this->getFormAction($renderable));
		$fields = $renderable->getFormFields();
		$renderer = $this->field_renderer;
		
		foreach ($fields as $field) {
			if ($field->getVisibility() & CoffeeBean::VISIBILITY_FORM) {
				if (null !== $r = $field->getEnforcedFieldRenderer()) {
					$form->addChild($r->renderForm($field));
				} else {
					$form->addChild($renderer->renderForm($field));
				}
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
		$renderer = $this->field_renderer;
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