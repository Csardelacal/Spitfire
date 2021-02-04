<?php namespace tests\spitfire\core\autoload;

use spitfire\autoload\NamespacedClassLocator;

class NamespacedclasslocatorTest extends \PHPUnit_Framework_TestCase
{
	
	private $locator;
	
	public function testLookingForControllers() {
		$locator = new NamespacedClassLocator('', spitfire()->getCWD() . '/bin/controllers', 'Controller');
		
		$this->assertNotEquals(false, $locator->getFilenameFor('HomeController'), 
				'The class Locator in spitfire should find the home controller.');
	}
	
}