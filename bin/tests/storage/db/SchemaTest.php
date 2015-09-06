<?php

class SchemaTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * Ensures that a schema, when created, has the default _id field.
	 */
	public function testCreate() {
		$schema = new \spitfire\storage\database\Schema('test');
		
		#Test if ID exists and is a Integer
		$this->assertInstanceOf(spitfire\model\Field::class, $schema->_id);
		$this->assertInstanceOf(IntegerField::class, $schema->_id);
		
		#Test if the name is actually test
		$this->assertEquals('test', $schema->getName());
	}
	
	/**
	 * 
	 * @expectedException privateException
	 */
	public function testReadingAnUnexistingField() {
		$schema = new \spitfire\storage\database\Schema('test');
		$schema->test;
	}
	
	public function testPrimary() {
		$schema = new \spitfire\storage\database\Schema('test');
		$this->assertContainsOnlyInstancesOf(\spitfire\model\Field::class, $schema->getPrimary());
	}
}