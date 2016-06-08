<?php namespace tests;

use PHPUnit_Framework_TestCase;
use Strings;

class StringTest extends PHPUnit_Framework_TestCase
{
	
	public function testSlugSpaces() {
		$this->assertEquals('a-string-with-spaces', Strings::slug('a string with spaces'));
		$this->assertEquals('a-string-with-spaces', Strings::slug('a string with  spaces'));
		$this->assertEquals('a-string-with-spaces', Strings::slug('a string with   spaces'));
	}
	
	public function testSlugSpecialChars() {
		$this->assertEquals('a-string-with-special-chars', Strings::slug('a string with spëcìal chàrs'));
		$this->assertEquals('a-string-with-special-chrs',  Strings::slug('a string with spëcìal chªrs'));
		$this->assertEquals('a-string-with-special-chrs',  Strings::slug('a_string_with spëcìal chªrs'));
	}
	
	public function testSlugUppercase() {
		$this->assertEquals('uppercase', Strings::slug('UPPERCASE'));
		$this->assertEquals('some-caps', Strings::slug('Some CaPS'));
	}
	
	/**
	 * Tests some of the most common usages of the camel2underscores function. This
	 * function is usually helpful when converting class names to environments 
	 * which are not case-sensitive.
	 * 
	 * @covers \Strings::camel2underscores
	 */
	public function testCamelCase2UnderscoreConversions() {
		$this->assertEquals('some_string', Strings::camel2underscores('someString'));
		$this->assertEquals('some_string', Strings::camel2underscores('SomeString'));
	}
	
	/**
	 * Tests the function that converts underscore separated names into camel case 
	 * identifiers.
	 * 
	 * @covers \Strings::underscores2camel
	 */
	public function testUnderscore2CamelCaseConversions() {
		$this->assertEquals('SomeString', Strings::underscores2camel('some_string'));
		$this->assertEquals('someString', Strings::underscores2camel('some_string', false));
	}
	
}
