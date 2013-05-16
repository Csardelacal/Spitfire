<?php

namespace tests;

use Test;

class dbCreateTest extends Test
{
	public function setUp() {
		db()->table('test')->destroy();
	}
	
	public function testNewDB() {
		$this->assertInstance(db(), 'spitfire\storage\database\DB');
	}
	
	public function testNewTable() {
		$this->assertInstance(db()->table('test'), 'spitfire\storage\database\Table');
	}
	
	public function testQuery() {
		$this->assertInstance(db()->table('test')->getAll(), 'spitfire\storage\database\Query');
	}
	
	public function testCreateRecord() {
		$record = db()->table('test')->newRecord();
		$record->content = 'Hello world';
		$record->store();
		
		$this->assertEquals($record->id, 1);
	}
	
	public function testFindRecord() {
		$this->assertInstance(db()->table('test')->get('id', 1)->fetch(), 'databaseRecord');
	}
	
	public function testDeleteRecord() {
		db()->table('test')->get('id', 1)->fetch()->delete();
		
		$this->assertEquals(db()->table('test')->get('id', 1)->fetch(), false);
		$this->assertEquals(count(db()->table('test')->get('id', 1)->fetchAll()), 0);
	}
}