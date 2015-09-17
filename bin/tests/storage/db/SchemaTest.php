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
	 * @expectedException \spitfire\exceptions\PrivateException
	 */
	public function testReadingAnUnexistingField() {
		$schema = new \spitfire\storage\database\Schema('test');
		$schema->test;
	}
	
	public function testPrimary() {
		$schema = new \spitfire\storage\database\Schema('test');
		$this->assertContainsOnlyInstancesOf(\spitfire\model\Field::class, $schema->getPrimary());
	}
	
	/**
	 * This test assumes that the table will be located inside a namespace. In 
	 * this case the schema should return a table name that contains hyphens instead
	 * of backslashes since tables do accept hyphens and don't accept backslashes.
	 */
	public function testComplexTableName() {
		$schema = new \spitfire\storage\database\Schema('test\test');
		$this->assertEquals('test\test', $schema->getName(), 'The schema name should be the class name without Model suffix.');
		$this->assertEquals('test-test', $schema->getTableName(), 'The table name should have replaced hyphens.');
	}
}