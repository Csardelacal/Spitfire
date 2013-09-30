<?php

namespace spitfire\storage\database\drivers;

use \spitfire\storage\database\RestrictionGroup;

class MysqlPDORestrictionGroup extends RestrictionGroup
{
	public function __toString() {
		return sprintf('(%s)', implode(' ' . $this->getType() .' ', $this->getRestrictions()));
	}
}