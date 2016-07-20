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
 * Localizes a currency, allowing the application to properly display an amount 
 * in a given language.
 * 
 * As opposed to the currency itself, this deals with localized printing of a 
 * currency as opposed to the currency itself.
 * 
 * For example, in Europe - most currencies are printed like 2,50€ while in the 
 * US or GB we have $2.50 or £2.50. To accomodate this different behaviors we need
 * to separate the actual currency from the format it's printed in.
 * 
 * There's another detail this class handles. You can use it to print USD like 
 * 2.50 USD in countries like Mexico where the $ symbol represents Pesos.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class CurrencyLocalizer
{
	
	const SYMBOL_BEFORE      = 1;
	const SYMBOL_AFTER       = 2;
	const SYMBOL_DECIMALSEP  = 3;
	
	private $decimalSeparator;
	
	private $thousandsSeparator;
	
	private $symbolPosition;
	
	private $specialTranslations = Array();
	
	/**
	 * 
	 * @param string $decimalSeparator
	 * @param string $thousandsSeparator
	 * @param string $symbolPosition
	 */
	public function __construct($decimalSeparator = '.', $thousandsSeparator = ',', $symbolPosition = CurrencyLocalizer::SYMBOL_AFTER) {
		$this->decimalSeparator   = $decimalSeparator;
		$this->thousandsSeparator = $thousandsSeparator;
		$this->symbolPosition     = $symbolPosition;
	}
	
	/**
	 * Formats a currency according to the provided settings. Please note, that 
	 * this class does not provide a mechanism to read the data given back in -
	 * so the behavior of this method is to be considered destructive.
	 * 
	 * @param float $amt
	 * @param Currency $currency
	 * @return string
	 */
	public function format($amt, Currency$currency) {
		
		#If there is a special case translation we will reset the format to prevent
		#the application from showing bogus data. Usually special translations come
		#after the number since they disambiguate the value.
		if (isset($this->specialTranslations[$currency->getISOCode()])) {
			$symbol = $this->specialTranslations[$currency->getISOCode()];
			$position = self::SYMBOL_AFTER;
		}
		else {
			$symbol   = $currency->getSymbol();
			$position = $this->symbolPosition;
		}
		
		#Format the number
		if ($position === self::SYMBOL_DECIMALSEP) {
			$number = number_format($amt, $currency->getDecimals(), $currency->getSymbol(), $this->thousandsSeparator);
		} 
		else {
			$number = number_format($amt, $currency->getDecimals(), $this->decimalSeparator, $this->thousandsSeparator);
		}
		
		#Depending on where the symbol is positioned we have a bunch of options.
		switch ($position) {
			case self::SYMBOL_BEFORE     : return $symbol . ' ' . $number;
			case self::SYMBOL_DECIMALSEP : return $number;
			case self::SYMBOL_AFTER      :
			default                      : return $number . ' ' . $symbol;
		}
	}
}