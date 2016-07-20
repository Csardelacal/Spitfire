<?php namespace spitfire\locale;

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

/**
 * Contains information that is locale safe about a currency, including the 
 * preferred symbol, ISO Code and decimal number count.
 * 
 * Please note that the use of this class does not replace common pitfalls that 
 * may arise and require additional handling on the application's end.
 * 
 * For example:
 * 
 * * This class does not manage the position of the symbol, wherever your locale
 *   may prefer to have the symbol depends on the locale itself.
 * 
 * * This class does not manage localization across different locales, for example,
 *   the dollar symbol is used by several regions in which the US$ is printed as
 *   "USD"
 */
class Currency
{
	
	private $symbol;
	
	private $isoCode;
	
	private $decimalCount;
	
	public function __construct($symbol, $isoCode, $decimalCount) {
		$this->symbol = $symbol;
		$this->isoCode = $isoCode;
		$this->decimalCount = $decimalCount;
	}
	
	public function getSymbol() {
		return $this->symbol;
	}
	
	public function getIsoCode() {
		return $this->isoCode;
	}
	
	public function getDecimalCount() {
		return $this->decimalCount;
	}
	
}