<?php namespace tests;

class StringTest extends \PHPUnit_Framework_TestCase
{
	
	public function testSlugSpaces() {
		$this->assertEquals('a-string-with-spaces', \Strings::slug('a string with spaces'));
		$this->assertEquals('a-string-with-spaces', \Strings::slug('a string with  spaces'));
		$this->assertEquals('a-string-with-spaces', \Strings::slug('a string with   spaces'));
	}
	
	public function testSlugSpecialChars() {
		$this->assertEquals('a-string-with-special-chars', \Strings::slug('a string with spëcìal chàrs'));
		$this->assertEquals('a-string-with-special-chrs',  \Strings::slug('a string with spëcìal chªrs'));
		$this->assertEquals('a-string-with-special-chrs',  \Strings::slug('a_string_with spëcìal chªrs'));
	}
	
	public function testSlugUppercase() {
		$this->assertEquals('uppercase', \Strings::slug('UPPERCASE'));
		$this->assertEquals('some-caps', \Strings::slug('Some CaPS'));
	}
	
}
