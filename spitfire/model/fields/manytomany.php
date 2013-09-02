<?php

use spitfire\model\Field;

class ManyToManyField extends ChildrenField
{
	
	private $meta;
	private $target;
	
	public function __construct($model) {
		$this->target = $model;
	}
	
	public function getRole() {
		return parent::getModel()->getName();
	}

	public function getTarget() {
		
		if($this->meta) return $this->target; //$this->meta;
		
		$src    = $this->getModel()->getName();
		$target = $this->target;
		
		$first  = ($src > $target)? $target : $src;
		$second = ($first == $src)? $target : $src;
		
		if (!$this->getTable()->getDb()->hasTable("{$first}_{$second}")) {
			
			$model = $this->meta = new Schema("{$first}_{$second}");
			unset($model->_id);

			$model->{$src}    = new Reference($src);
			$model->{$target} = new Reference($target);

			$model->{$src}->setPrimary(true);
			$model->{$target}->setPrimary(true);

			#Register the table
			$this->getModel()->getTable()->getDb()->table($model);
		}
		else {
			$this->meta = $this->getTable()->getDb()->table("{$first}_{$second}")->getModel();
		}
		
		return $this->target = $this->getModel()->getTable()->getDb()->table($target)->getModel();//$this->meta;
	}
	
	public function getModelField($schema) {
		return $this->meta->getField($schema->getName());
	}
	
	/**
	 * Returns the table that connects the two tables to form a many to many 
	 * relationship
	 * 
	 * @return spitfire\storage\database\Table
	 */
	public function getBridge() {
		if ($this->meta) return $this->meta;
		
		$this->getTarget();
		return $this->meta;
	}

	public function getDataType() {
		return Field::TYPE_BRIDGED;
	}
	
}
