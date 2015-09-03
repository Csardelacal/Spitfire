<?php namespace tests\spitfire\core;

use spitfire\core\Headers;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
	
	public function testContentType() {
		
		$t = new Headers();
		
		$t->contentType('php');
		$this->assertEquals('text/html;charset=utf-8', $t->get('Content-type'));
		
		$t->contentType('html');
		$this->assertEquals('text/html;charset=utf-8', $t->get('Content-type'));
		
		$t->contentType('json');
		$this->assertEquals('application/json;charset=utf-8', $t->get('Content-type'));
		
		$t->contentType('xml');
		$this->assertEquals('application/xml;charset=utf-8', $t->get('Content-type'));
		
	}
	
	/**
	 * Test whether the state shorthand function rewrites the states properly. If
	 * it does, the app should be returning 200 OK as status when you pass 200 to
	 * it.
	 */
	public function testStatus() {
		$t = new Headers();
		$t->status('200');
		$this->assertEquals('200 OK', $t->get('Status'));
	}
	
	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testInvalidStatus() {
		$t = new Headers();
		$t->status('22');
	}
	
}

