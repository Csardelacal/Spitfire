<?php

class SchemaTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * Ensures that a schema, when created, has the default _id field.
	 */
	public function testCreate() {
		$schema = new \spitfire\storage\database\Schema('test');
		$this->assertInstanceOf(spitfire\model\Field::class, $schema->_id);
		$this->assertInstanceOf(IntegerField::class, $schema->_id);
	}
	
	/**
	 * 
	 * @expectedException privateException
	 */
	public function testReadingAnUnexistingField() {
		$schema = new \spitfire\storage\database\Schema('test');
		$schema->test;
	}
}