<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\CompositeRestriction;

class MysqlPDOCompositeRestriction extends CompositeRestriction
{
	
	public function __toString() {
		return sprintf('(%s)', implode(' AND ', $this->getSimpleRestrictions()));
	}
	
}