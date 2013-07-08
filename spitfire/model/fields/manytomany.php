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
		
		if (db()->hasTable("{$first}_{$second}")) 
			return $this->meta = db()->table("{$first}_{$second}")->getModel();
		
		$model = $this->meta = db()->table(new ModelMeta("{$first}_{$second}"))->getModel();
		unset($model->_id);
		
		$model->{$src}    = new Reference($src);
		$model->{$target} = new Reference($target);
		
		$model->{$src}->setPrimary(true);
		$model->{$target}->setPrimary(true);
		
		$this->getModel()->getTable()->getDb()->table($model)->makeFields();
		return $this->target = $this->getModel()->getTable()->getDb()->table($target)->getModel();//$this->meta;
	}
	
	public function getBridge() {
		return $this->meta;
	}

	public function getDataType() {
		return Field::TYPE_BRIDGED;
	}
	
}
