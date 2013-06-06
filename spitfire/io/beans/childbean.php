<?php

namespace spitfire\io\beans;

use Model;
use \CoffeeBean;

class ChildBean extends Field 
{
	private $bean;
	private $role;
	
	public function setRelation($bean, $role = null) {
		$this->bean = $bean;
		$this->role = $role;
	}
	
	public function getRelation() {
		return Array('bean' => $this->bean, 'role' => $this->role);
	}
	
	public function getRequestValue() {
		$data = $_POST[$this->bean];
		return $data;
	}
	
	public function store() {
		$data   = $this->getRequestValue();
		$record = $this->getBean()->getRecord();
		
		foreach ($data as $pk => $post) {
			$child = CoffeeBean::getBean($this->bean);
			$model = Model::getInstance($child->model);
			$table  = db()->table($model);
			$query  = $record->getChildren($table, $this->role);
			$primary = $table->getPrimaryKey();
			$pk = explode(':', $pk);
			
			foreach ($primary as $field => $meta) {
				$query->addRestriction($field, array_shift($pk));
			}
			
			$r = $query->fetch();
			foreach ($post as $key => $value) {
				$f = $child->getField($key);
				if ($f instanceof ReferenceField) {
					$reference = Model::getInstance($f->getBean()->model)->getField($f->getModelField());
					$table     = db()->table($reference->getTarget());
					$pk        = $table->getPrimaryKey();
					$search    = explode('|', $value);
					$query     = $table->get(array_shift($pk), array_shift($search));

					if (count($pk)) {
						$query->addRestriction(array_shift($pk), array_shift($search));
					}

					$r->{$f->getModelField()} = $query->fetch();
				}
				else {
					$r->{$f->getModelField()} = $value;
				}
			}
			$r->store();
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