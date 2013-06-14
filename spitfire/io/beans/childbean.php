<?php

namespace spitfire\io\beans;

use Model;
use \CoffeeBean;

class ChildBean extends Field 
{
	private $bean;
	private $role;
	
	private $min_entries = 0;
	
	public function setRelation($bean, $role = null) {
		$this->bean = $bean;
		$this->role = $role;
		return $this;
	}
	
	public function getRelation() {
		return Array('bean' => $this->bean, 'role' => $this->role);
	}
	
	public function getRequestValue() {
		if ($_SERVER['REQUEST_METHOD'] != 'POST') return Array();
		$data = $_POST[$this->bean];
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
			
			$child = CoffeeBean::getBean($this->bean);
			$model = Model::getInstance($child->model);
			$table  = db()->table($model);
			
			if (substr($pk, 0, 5) == '_new_') {
				$r = $table->newRecord();
			}
			else {
				$query  = $record->getChildren($table, $this->role);
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
					if ($f->getModelField() == $this->getBean()->getName()) {
						$r->{$f->getModelField()} = $this->getBean()->getRecord();
						
					}
					else {
						$reference = Model::getInstance($f->getBean()->model)->getField($f->getModelField());
						$table     = db()->table($reference->getTarget());
						$pk        = $table->getPrimaryKey();
						$search    = explode('|', $value);
						$query     = $table->get(array_shift($pk), array_shift($search));

						while (count($pk)) {
							$query->addRestriction(array_shift($pk), array_shift($search));
						}

						$r->{$f->getModelField()} = $query->fetch();
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