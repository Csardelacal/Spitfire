<?php namespace tests\spitfire\storage\db;

use PHPUnit_Framework_TestCase;

class TableTest extends PHPUnit_Framework_TestCase
{
	
	private $db;
	
	/**
	 * The table we're testing.
	 *
	 * @var \spitfire\storage\database\Table
	 */
	private $table;
	private $schema;
	
	public function setUp() {
		//Just in case Mr. Bergmann decides to add code to the setUp
		parent::setUp();
		
		//TODO: This needs to be replaced with logic that actually is properly testable.
		//Currently there is no DB mock driver. Not sure if I should create one or just test different drivers
		$this->db = db();
		
		$this->schema = new \spitfire\storage\database\Schema('test');
		
		$this->schema->field1 = new \IntegerField(true);
		$this->schema->field2 = new \StringField(255);
		
		$this->table = new \spitfire\storage\database\drivers\MysqlPDOTable($this->db, $this->schema);
	}
	
	public function testGetField() {
		$this->assertInstanceOf(\spitfire\storage\database\DBField::class, $this->table->getField('field1'));
		$this->assertInstanceOf(\spitfire\storage\database\DBField::class, $this->table->getField('field2'));
		
		//This checks that the table identifies and returns when an object is provided
		$this->assertInstanceOf(\spitfire\storage\database\DBField::class, $this->table->getField($this->table->getField('field2')));
	}
	
	/**
	 * @expectedException \spitfire\exceptions\PrivateException
	 */
	public function tsetGetUnexistingFieldByName() {
		$this->table->getField('unexistingfield');
	}
	
	/**
	 * @expectedException \spitfire\exceptions\PrivateException
	 */
	public function testGetUnexistingFieldByObject() {
		$schema = new \spitfire\storage\database\Schema('notreal');
		$table  = $this->db->table($schema);
		$field = new \IntegerField();
		$field->setModel($schema);
		$this->table->getField(new \spitfire\storage\database\drivers\mysqlPDOField($field, 'notexisting'));
	}


	public function testFieldTypes() {
		$this->assertEquals(\spitfire\model\Field::TYPE_STRING, $this->table->getField('field2')->getLogicalField()->getDataType());
	}
	
}
