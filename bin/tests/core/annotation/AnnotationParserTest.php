<?php namespace tests\core\annotation;

use PHPUnit_Framework_TestCase;
use spitfire\core\annotations\AnnotationParser;


class AnnotationParserTest extends PHPUnit_Framework_TestCase
{
	
	public function testParser() {
		
		$string = "/**\n * @param test A \n * @param test B \n */";
		$parser = new AnnotationParser();
		
		$annotations = $parser->parse($string);
		
		#Test the element is actually there
		$this->assertArrayHasKey('param', $annotations);
		
		#Ensure it did parse the same annotation twice and properly structure the array
		$this->assertCount(1, $annotations);
		$this->assertCount(2, $annotations['param']);
		
		#Test the value is what we expect
		$this->assertEquals('test', $annotations['param'][0][0]);
		$this->assertEquals('A',    $annotations['param'][0][1]);
		$this->assertEquals('B',    $annotations['param'][1][1]);
		
	}
	
}
