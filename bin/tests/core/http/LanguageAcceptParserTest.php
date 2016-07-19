<?php namespace tests\spitfire\core\http;

/* 
 * The MIT License
 *
 * Copyright 2016 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class LanguageAcceptParserTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * 
	 * @covers \spitfire\core\http\LanguageAcceptParser::parse()
	 * @covers \spitfire\core\http\LanguageAccept::validateFormat()
	 * @covers \spitfire\core\http\LanguageAccept::makeLocales()
	 * @covers \spitfire\core\http\LanguageAccept::makePriority()
	 */
	public function testParser() {
		$parser = new \spitfire\core\http\LanguageAcceptParser('ru', 'da, en-gb;q=0.8, en;q=0.7');
		$res    = $parser->parse();
		
		$this->assertEquals('da', reset($res)->getLanguage());
		$this->assertEquals('ru', end($res)->getLanguage());
		
		$this->assertEquals('gb', $res[1]->getLocale());
	}
}