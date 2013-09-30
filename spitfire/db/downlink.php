<?php

namespace spitfire\storage\database;

class Downlink extends Uplink
{
	
	
	public function getRestrictions() {
		$group    = $this->getSrc()->restrictionGroupInstance()->setType(RestrictionGroup::TYPE_AND);
		$physical = $this->getRelation()->getPhysical();
		
		foreach ($physical as $field) {
			$group->putRestriction(
				$this->getSrc()->restrictionInstance(//Refers to MySQL
					$this->getSrc()->queryFieldInstance($field), //This two can be put in any order
					$this->getTarget()->queryFieldInstance($field->getReferencedField()), 
					Restriction::EQUAL_OPERATOR
				 )
			);
		}
		
		return $group;
	}
	
}