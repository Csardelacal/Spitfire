<?php

namespace spitfire\io\beans;

use Model;
use \CoffeeBean;

class ChildBean extends Field 
{
	
	private $min_entries = 0;
	
	public function getRequestValue() {
		if ($_SERVER['REQUEST_METHOD'] != 'POST') return Array();
		
		$field = $this->getField();
		/* @var $field \ChildrenField */
		
		$data = $_POST[$field->getTarget()->getTable()->getBean()->getName()];
		return array_filter($data);
	}
	
	public function getMinimumEntries() {
		return $this->min_entries;
	}
	
	public function setMinimumEntries($amt) {
		$this->min_entries = $amt;
		return $this;
	}
	
	public function store() {
		$data   = $this->getRequestValue();
		$record = $this->getBean()->getRecord();
		
		foreach ($data as $pk => $post) {
			$post = array_filter($post);
			if (empty($post)) continue;
			
			$child = $this->getField()->getTarget()->getTable()->getBean();
			$model = $this->getField()->getModel();
			$table = $this->getField()->getTarget()->getTable();
			
			if (substr($pk, 0, 5) == '_new_') {
				$r = $table->newRecord();
			}
			else {
				$ref_fields = $this->getField()->getReferencedFields();
				$query = $this->getField()->getTarget()->getTable()->getAll();
				$group = $query->group();
				foreach ($ref_fields as $f) $group->addRestriction($f->getName(), $record);
				
				$primary = $table->getPrimaryKey();
				$pk = explode(':', $pk);

				foreach ($primary as $field => $meta) {
					$query->addRestriction($field, array_shift($pk));
				}

				$r = $query->fetch();
			}
			
			$fields = $child->getFields();
			foreach ($fields as $key => $f) {
				$value = $post[$key];
				$f = $child->getField($key);
				if ($f instanceof ReferenceField) {
					if ($f->getField()->getTarget() == $this->getBean()->getTable()->getModel()) {
						$r->{$f->getField()->getName()} = $this->getBean()->getRecord();
						
					}
					else {
						$reference = $f->getField()->getTarget();
						$table     = $reference->getTable();
						
						$r->{$f->getFieldName()} = $table->getById($value);
					}
				}
				else {
					$r->{$f->getModelField()} = $value;
				}
			}
			if ($r instanceof \databaseRecord) {
				$r->store();
			} 
		}
	}
	
	public function getDefaultValue() {
		//TODO: Implement
		return null;
	}
	
	public function getVisibility() {
		return CoffeeBean::VISIBILITY_FORM;
	}


	public function __toString() {
		$target = Model::getInstance($this->getBean()->model);
		$srcBean = CoffeeBean::getBean($this->getModelField());
		$src    = Model::getInstance($srcBean->model);
		
		$relation = $src->getReference($target);
		$fields   = $relation->getFields();
		
		
		$query = db()->table($src)->getAll();
		foreach ($fields as $field) {
			$query->addRestriction($field->getName(), $this->getBean()->getField($field->getReferencedField()->getName())->getValue());
		}
		
		$data = $query->fetchAll();
		
		return strval($srcBean->makeList($data));
	}
	
}