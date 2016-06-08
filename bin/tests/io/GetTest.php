<?php namespace tests\spitfire\io;

class GetTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Usually PHP will handle isset differently. But it's in our best interest
	 * to tell the user whether data was sent in the variable.
	 * 
	 * As opposed to empty(), which will report whether the variable is null, 0 or
	 * an empty string - our isset implementation for get will return true on null
	 * values.
	 * 
	 * @covers \spitfire\io\Get::__isset
	 */
	public function testIsset() {
		$get = new \spitfire\io\Get(Array('a' => 123, 'c' => null));
		
		$this->assertEquals(false, isset($get->b));
		$this->assertEquals(false, isset($get['b']));
		
		$this->assertEquals(true, isset($get->c));
		$this->assertEquals(true, isset($get['c']));
		
		$this->assertEquals(true, isset($get->a));
		$this->assertEquals(true, isset($get['a']));
	}
	
	/**
	 * Tests whether the empty function works on the individual elements the way
	 * it's supposed to.
	 * 
	 * @covers \spitfire\io\Get::offsetExists
	 * @covers \spitfire\io\Get::offsetGet
	 */
	public function testEmpty() {
		$get = new \spitfire\io\Get(Array('a' => 123, 'c' => null));
		
		$this->assertEquals(true, empty($get->b));
		$this->assertEquals(true, empty($get['b']));
		
		$this->assertEquals(true, empty($get->c));
		$this->assertEquals(true, empty($get['c']));
		
		$this->assertEquals(false, empty($get->a));
		$this->assertEquals(false, empty($get['a']));
	}
	
}
