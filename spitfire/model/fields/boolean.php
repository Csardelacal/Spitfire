<?php

use spitfire\model\Field;
use spitfire\Model;
use spitfire\model\adapters\BooleanAdapter;

/**
 * This class represents a column in the database capable of holding boolean data.
 * Your DBMS can read and write only true or false from the adapter delivered
 * by this element.
 */
class BooleanField extends Field
{
	/**
	 * Indicates the type of field the database column is. This indicates to the 
	 * DBMS what kind of column it needs to allocate to store this content.
	 * 
	 * @return string
	 */
	public function getDataType() {
		return Field::TYPE_BOOLEAN;
	}
	
	/**
	 * This method receives a model to which it shall create a connection. The
	 * adapter can then hold information that is useful to the db.
	 * 
	 * @param Model $model
	 * @return \spitfire\model\adapters\BooleanAdapter
	 */
	public function getAdapter(Model $model) {
		return new BooleanAdapter($this, $model);
	}

	public function getConnectorQueries(\spitfire\storage\database\Query $parent) {
		return Array();
	}

}