<?php

namespace spitfire\io\beans;

use Model;
use \CoffeeBean;

class ChildBean extends Field 
{
	private $relation;
	
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