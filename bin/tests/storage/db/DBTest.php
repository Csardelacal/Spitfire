<?php namespace tests\spitfire\storage\db;

use PHPUnit_Framework_TestCase;

class DBTest extends PHPUnit_Framework_TestCase
{
	
	public function testdb() {
		$this->assertInstanceOf('spitfire\storage\database\DB', db());
	}
	
	
}