<?php

use spitfire\model\Field;
use spitfire\Model;
use spitfire\model\adapters\StringAdapter;

/**
 * Represents a field holding a big chunk of text. The essential difference 
 * between those is the way they are stored in the database and requested from
 * the user.
 * 
 * This field uses Text inside the databases when possible which as opossed to 
 * varchar and similar doesn't reserve a fixed amount of space inside the tables
 * and is located outside it.
 * 
 * It also is usually presented by the beans as a Textarea which allows multiline
 * text and other improvements for the user.
 */
class TextField extends Field
{
	/**
	 * Indicates the type of data located in this field. This returns a static 
	 * 'text' which allows the database to accomodate the required column in the
	 * table.
	 * 
	 * @return string
	 */
	public function getDataType() {
		return Field::TYPE_TEXT;
	}
	
	/**
	 * Returns the adapter used by this field. Please note that it uses the same 
	 * adapter as StringField, this is due to the data being exactly the same from
	 * a PHP end and just changing in how the models and the DBMS retrieve the 
	 * data.
	 * 
	 * @param Model $model
	 * @return \spitfire\model\adapters\StringAdapter
	 */
	public function getAdapter(Model $model) {
		return new StringAdapter($this, $model);
	}

	public function getConnectorQueries(\spitfire\storage\database\Query $parent) {
		return Array();
	}

}