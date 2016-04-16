<?php namespace tests\spitfire\storage\db;

use IntegerField;
use PHPUnit_Framework_TestCase;
use spitfire\storage\database\drivers\mysqlPDOField;
use spitfire\storage\database\drivers\MysqlPDOQuery;
use spitfire\storage\database\drivers\MysqlPDOQueryField;
use spitfire\storage\database\drivers\MysqlPDORestriction;
use spitfire\storage\database\drivers\MysqlPDORestrictionGroup;
use spitfire\storage\database\drivers\MysqlPDOTable;
use spitfire\storage\database\Schema;

class RestrictionGroupTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * This test creates a 
	 */
	public function testClone() {
		
		$table = new MysqlPDOTable(db(), new Schema('test'));
		$query = new MysqlPDOQuery($table);
		$field = new mysqlPDOField(new IntegerField(), 'test');
		$queryfield = new MysqlPDOQueryField($query, $field);
		
		$groupa = new MysqlPDORestrictionGroup($query);
		$groupa->putRestriction(new MysqlPDORestriction($groupa, $queryfield, 'A'));
		
		$groupb = clone $groupa;
		
		$this->assertEquals($groupa->getRestriction(0)->getParent() === $groupb->getRestriction(0)->getParent(), 
				  false, 'The two restrictions from two cloned queries should have different parents');
		
		$this->assertEquals($groupa->getRestriction(0)->getQuery() === $groupb->getRestriction(0)->getQuery(), 
				  true, 'The two restrictions should share a common query');
	}
	
}