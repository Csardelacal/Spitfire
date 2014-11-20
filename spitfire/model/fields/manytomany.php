<?php

use spitfire\model\Field;
use spitfire\model\adapters\ManyToManyAdapter;

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
		
		if ($src === $target) { $targetalias = $target . '_1'; }
		else                  { $targetalias = $target; }
		
		if (!$this->getTable()->getDb()->hasTable("{$first}_{$second}")) {
			
			$model = $this->meta = new Schema("{$first}_{$second}");
			unset($model->_id);

			$model->{$src}         = new Reference($src);
			$model->{$targetalias} = new Reference($target);

			$model->{$src}->setPrimary(true);
			$model->{$targetalias}->setPrimary(true);

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
		if ($this->meta) { return $this->meta; }
		
		$this->getTarget();
		return $this->meta;
	}

	public function getDataType() {
		return Field::TYPE_BRIDGED;
	}
	
	public function getAdapter(\Model $model) {
		return new ManyToManyAdapter($this, $model);
	}
	
	public function getConnectorQueries(spitfire\storage\database\Query$parent) {
		$query = $this->getTarget()->getTable()->getAll();
		$query->setAliased(true);
		
		if ($this->target !== $this->getModel()) {
			#In case the models are different we just return the connectors via a simple route.
			$route  = $this->getBridge()->getTable()->getAll();
			$fields = $this->getBridge()->getFields();
			$route->setAliased(true);
			
			foreach ($fields as $field) {
				if ($field->getTarget() === $this->getModel()) {
					$physical = $field->getPhysical();
					foreach ($physical as $p) { $route->addRestriction($route->queryFieldInstance($p), $parent->queryFieldInstance($p->getReferencedField()));}
				} else {
					$physical = $field->getPhysical();
					foreach ($physical as $p) { $query->addRestriction($route->queryFieldInstance($p), $query->queryFieldInstance($p->getReferencedField()));}
				}
			}
			return Array($route, $query);
			
		} else {
			#In case the models are the same, well... That's hell
			$route1 = $this->getBridge()->getTable()->getAll();
			$route2 = $this->getBridge()->getTable()->getAll();
			$fields = $this->getBridge()->getFields();
			
			#Alias the routes so they don't collide
			$route1->setAliased(true);
			$route2->setAliased(true);
			
			$f1  = reset($fields);
			$f2  = end($fields);
			$f1p = $f1->getPhysical();
			$f2p = $f2->getPhysical();
			
			#Start with routes from src
			foreach ($f1p as $p) {$route1->addRestriction($route1->queryFieldInstance($p), $parent->queryFieldInstance($p->getReferencedField()));}
			foreach ($f2p as $p) {$route2->addRestriction($route2->queryFieldInstance($p), $parent->queryFieldInstance($p->getReferencedField()));}
			
			#Exclude repeated results from Route2
			$group = $route2->group(\spitfire\storage\database\RestrictionGroup::TYPE_OR);
			foreach ($f1p as $k => $v) {$group->addRestriction($route2->queryFieldInstance($v), $route2->queryFieldInstance($f2p[$k]), '<>');}
			
			#Link back
			$groupback = $query->group(spitfire\storage\database\RestrictionGroup::TYPE_OR);
			$r1group   = $groupback->group(spitfire\storage\database\RestrictionGroup::TYPE_AND);
			$r2group   = $groupback->group(spitfire\storage\database\RestrictionGroup::TYPE_AND);
			
			#Note that the fields are now swaped
			foreach ($f2p as $p) {$r1group->addRestriction($route1->queryFieldInstance($p), $query->queryFieldInstance($p->getReferencedField()));}
			foreach ($f1p as $p) {$r2group->addRestriction($route2->queryFieldInstance($p), $query->queryFieldInstance($p->getReferencedField()));}
			
			return Array($route1, $route2, $query);
		}
	}
	
}
