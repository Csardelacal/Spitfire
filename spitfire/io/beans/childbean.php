<?php

namespace spitfire\io\beans;

use Model;
use \CoffeeBean;

/**
 * This class allows a bean to receive data that belongs to this but is handled
 * by another bean. In this case the childbean will take care of handling the 
 * data and returning it to the parent bean.
 * 
 * Basically this generates what other apps call sub-forms, a form that allows 
 * you to handle data in a clean way.
 * 
 * @author César de la Cal <cesar@magic3w.com>
 * @since 0.1
 */
class ChildBean extends Field 
{
	
	/**
	 * This variable indicates the minimum amount of subforms this child should 
	 * display when rendered. By default childbeans display the number of elements
	 * plus another empty spot to add a new one. If this is different than 0 it 
	 * will display at least as many as this requires.
	 * 
	 * @var int
	 */
	private $min_entries = 0;
	
	/**
	 * Returns the data posted to this childbean. This will be an array containing
	 * the data sent by the form as arrays.
	 * 
	 * @return mixed[]
	 */
	public function getRequestValue() {
		if ($_SERVER['REQUEST_METHOD'] != 'POST') return Array();
		
		$field = $this->getField();
		/* @var $field \ChildrenField */
		
		$data = $_POST[$field->getName()];
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
			
			$table = $this->getField()->getTarget()->getTable();
			$child = $table->getBean(true);
			
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
			
			$child->setParent($this);
			$child->setDBRecord($r);
			$child->setPostData($post);
			$child->updateDBRecord();
			$child->getRecord()->store();
			
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